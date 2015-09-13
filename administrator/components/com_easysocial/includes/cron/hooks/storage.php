<?php
/**
* @package		EasySocial
* @copyright	Copyright (C) 2010 - 2015 Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* EasySocial is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');

class SocialCronHooksStorage extends EasySocial
{
	/**
	 * Executes all remote storage
	 *
	 * @since	1.3
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function execute(&$states)
	{
		// Sync cached images from links to remote location
		$states[] = $this->syncLinkImages();

		// Offload photos to remote location
		$states[] = $this->syncPhotos();

		// Process avatar storages here
		$states[] = $this->syncAvatars();

		// Process file storages here
		$states[] = $this->syncFiles();
	}

	/**
	 * Retrieves the storage type
	 *
	 * @since	1.3
	 * @access	public
	 * @param	string
	 * @return
	 */
	private function getStorageType()
	{
		static $type = null;

		if (is_null($type)) {
			$type = $this->config->get('storage.photos', 'joomla');
		}

		return $type;
	}

	/**
	 * Retrieves the storage library
	 *
	 * @since	1.3
	 * @access	public
	 * @param	string
	 * @return
	 */
	private function getStorageLibrary()
	{
		return FD::storage($this->getStorageType());
	}

	/**
	 * Retrieves the limit on the number of files to sync
	 *
	 * @since	1.3
	 * @access	public
	 * @param	string
	 * @return
	 */
	private function getUploadLimit()
	{
		$type = $this->getStorageType();
		$limit = (int) $this->config->get('storage.' . $type . '.limit');

		return $limit;
	}

	/**
	 * Determines if we should delete the local file
	 *
	 * @since	1.3
	 * @access	public
	 * @param	string
	 * @return
	 */
	private function deleteable()
	{
		$storageType = $this->getStorageType();
		$delete = $this->config->get('storage.' . $storageType . '.delete');

		return $delete;
	}

	/**
	 * Creates a new log entry when a file is uploaded
	 *
	 * @since	1.3
	 * @access	public
	 * @param	string
	 * @return
	 */
	private function log($id, $type, $success)
	{
		$storageType = $this->getStorageType();

		// Add this to the storage logs
		$log = FD::table('StorageLog');
		$log->object_id = $id;
		$log->object_type = $type;
		$log->target = $storageType;
		$log->state = $success;
		$log->created = FD::date()->toSql();

		return $log->store();
	}

	/**
	 * Retrieves the list of log items
	 *
	 * @since	1.2
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function getFailedObjects($objectType, $state = SOCIAL_STATE_UNPUBLISHED)
	{
		$db = FD::db();
		$sql = $db->sql();
		$sql->select('#__social_storage_log');
		$sql->column('object_id');
		$sql->where('object_type', $objectType);
		$sql->where('state', $state);

		$db->setQuery($sql);

		$ids = $db->loadColumn();

		return $ids;
	}

	/**
	 * Synchronizes cached images from local storage to remote storage
	 *
	 * @since	1.3
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function syncLinkImages()
	{
		$storageType = $this->getStorageType();

		// If site is configured to storage in joomla, we don't need to do anything
		if ($storageType == 'joomla') {
			return JText::_('Current photos storage is set to local.');
		}

		// Get the storage library
		$storage = $this->getStorageLibrary();
		$limit = $this->getUploadLimit();

		// Get a list of items that should be excluded
		$exclusion = $this->getFailedObjects('linkimages');

		// Get a list of cached images to be synchronized over.
		$model = FD::model('Links');
		$options = array('storage' => SOCIAL_STORAGE_JOOMLA, 'limit' => $limit, 'exclusion' => $exclusion);
		$images = $model->getCachedImages($options);

		if (!$images) {
			return JText::_('No cached link images to sync with Amazon S3 right now.');
		}

		$states = array();
		$total = 0;

		foreach ($images as $image) {

			$state = $storage->push($image->internal_url, $image->getAbsolutePath(), $image->getRelativePath());

			if ($state) {

				if ($this->deleteable()) {
					JFile::delete($image->getAbsolutePath());
				}

				// Store the new storage type
				$image->storage = $storageType;
				$image->store();

				$total += 1;
			}

			$states[] = $state;

			// Create a log for this item
			$this->log($image->id, 'linkimage', $state);
		}

		if ($total > 0) {
			return JText::sprintf('%1s cached images uploaded to remote storage', $total);
		}

		return JText::sprintf('No cached images to upload to remote storage');
	}

	/**
	 * Synchronizes photos from local storage to remote storage.
	 *
	 * @since	1.2
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function syncPhotos()
	{
		$storageType = $this->getStorageType();

		// If site is configured to storage in joomla, we don't need to do anything
		if ($storageType == 'joomla') {
			return JText::_('Current photos storage is set to local.');
		}

		// Load up the storage library
		$storage = FD::storage($storageType);

		// Get the number of files to process at a time
		$limit = $this->getUploadLimit();

		// Get a list of photos that failed during the transfer
		$exclusion = $this->getFailedObjects('photos');

		// Get a list of files to be synchronized over.
		$model = FD::model('Photos');
		$options = array(
						'pagination'	=> $limit,
						'storage'		=> SOCIAL_STORAGE_JOOMLA,
						'ordering'		=> 'created',
						'sort' 			=> 'asc',
						'exclusion'		=> 	$exclusion
					);

		// Get a list of photos to sync to amazon
		$photos = $model->getPhotos($options);
		$total = 0;

		if (!$photos) {
			return JText::_('No photos to upload to Amazon S3 right now.');
		}

		// Get list of allowed photos
		$allowed = array('thumbnail', 'large', 'square', 'featured', 'medium', 'original', 'stock');

		foreach ($photos as $photo) {

			// Load the album
			$album = FD::table('Album');
			$album->load($photo->album_id);

			// If the album no longer exists, skip this
			if (!$album->id) {
				continue;
			}

			// Get the base path for the album
			$basePath = $photo->getStoragePath($album);
			$states = array();

			// Now we need to get all the available files for this photo
			$metas = $model->getMeta($photo->id, SOCIAL_PHOTOS_META_PATH);

			// Go through each meta
			foreach ($metas as $meta) {

				// To prevent some faulty data, we need to manually reconstruct the path here.
				$absolutePath = $meta->value;
				$file = basename($absolutePath);
				$container = FD::cleanPath($this->config->get('photos.storage.container'));

				// Reconstruct the path to the source file
				$source = JPATH_ROOT . '/' . $container . '/' . $album->id . '/' . $photo->id . '/' . $file;

				// To prevent faulty data, manually reconstruct the path here.
				$dest = $container . '/' . $album->id . '/' . $photo->id . '/' . $file;
				$dest = ltrim( $dest , '/' );

				// We only want to upload certain files
				if (in_array($meta->property, $allowed)) {
					// Upload the file to the remote storage now
					$state = $storage->push($photo->title . $photo->getExtension(), $source, $dest);

					// Delete the source file if successfull and configured to do so.
					if ($state && $this->deleteable()) {
						JFile::delete($source);
					}

					$states[] = $state;
				}
			}

			$success = !in_array(false, $states);

			// If there are no errors, we want to update the storage for the photo
			if ($success) {

				$photo->storage = $storageType;
				$state = $photo->store();

				// if photo storage successfully updated to amazon, we need to update the cached object in stream_item.
				// Find and update the object from stream_item.
				$stream = FD::table('StreamItem');
				$options = array('context_type' => SOCIAL_TYPE_PHOTO, 'context_id' => $photo->id);
				$exists = $stream->load($options);

				if ($exists) {
					$stream->params = FD::json()->encode($photo);
					$stream->store();
				}

				$total 	+= 1;
			}

			// Add this to the storage logs
			$this->log($photo->id, 'photos', $success);
		}

		if ($total > 0) {
			return JText::sprintf('%1s photos uploaded to remote storage', $total);
		}

		return JText::sprintf('No photos to upload to remote storage');
	}

	/**
	 * Synchronizes files to remote storage
	 *
	 * @since	1.2
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function syncFiles()
	{
		$storageType = $this->getStorageType();

		if ($storageType == 'joomla') {
			return JText::_('No files to upload to Amazon S3 right now.');
		}

		// Get the storage library
		$storage = $this->getStorageLibrary();

		// Get the number of files to process at a time
		$limit = $this->getUploadLimit();

		// Get a list of files to be synchronized over.
		$model = FD::model('Files');

		// Get a list of excluded avatars that previously failed.
		$exclusion = $this->getFailedObjects('files');
		$options = array('storage' => SOCIAL_STORAGE_JOOMLA, 'limit' => 10, 'exclusion' => $exclusion, 'ordering' => 'created', 'sort' => 'asc');

		$files = $model->getItems($options);
		$total = 0;

		foreach ($files as $file) {

			// Get the source file
			$source = $file->getStoragePath() . '/' . $file->hash;

			// Get the destination file
			$dest = $file->getStoragePath(true) . '/' . $file->hash;

			$success = $storage->push($file->name, $source, $dest);

			if ($success) {

				// Once the file is uploaded successfully delete the file physically.
				if ($this->deleteable()) {
					JFile::delete($source);
				}

				// Do something here.
				$file->storage = $storageType;
				$file->store();

				$total	+= 1;
			}

			// Create a new storage log for this transfer
			$this->log($file->id, 'files', $success);
		}

		if ($total > 0) {
			return JText::sprintf('%1s files uploaded to remote storage', $total);
		}

		return JText::_('Nothing to process for files');
	}

	/**
	 * Synchronizes avatars from the site over to remote storage
	 *
	 * @since	1.2
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function syncAvatars()
	{
		$storageType = $this->getStorageType();

		if ($storageType == 'joomla') {
			return JText::_('Current avatar storage is set to local.');
		}

		// Get the storage library
		$storage = $this->getStorageLibrary();

		// Get the number of files to process at a time
		$limit = $this->getUploadLimit();

		// Get a list of excluded avatars that previously failed.
		$exclusion 	= $this->getFailedObjects('avatars');

		// Get a list of avatars to be synchronized over.
		$model = FD::model('Avatars');
		$options = array('limit' => $limit , 'storage' => SOCIAL_STORAGE_JOOMLA , 'uploaded' => true, 'exclusion' => $exclusion);
		$avatars = $model->getAvatars($options);
		$total = 0;

		if (!$avatars) {
			return JText::_('No avatars to upload to Amazon S3 right now.');
		}

		foreach($avatars as $avatar) {

			$small = $avatar->getPath( SOCIAL_AVATAR_SMALL , false );
			$medium = $avatar->getPath( SOCIAL_AVATAR_MEDIUM , false );
			$large = $avatar->getPath( SOCIAL_AVATAR_LARGE , false );
			$square = $avatar->getPath( SOCIAL_AVATAR_SQUARE , false );

			$smallPath 	= JPATH_ROOT . '/' . $small;
			$mediumPath	= JPATH_ROOT . '/' . $medium;
			$largePath	= JPATH_ROOT . '/' . $large;
			$squarePath	= JPATH_ROOT . '/' . $square;

			$success = false;

			if (
				$storage->push($avatar->id, $smallPath, $small) &&
				$storage->push($avatar->id, $mediumPath, $medium) &&
				$storage->push($avatar->id, $largePath, $large) &&
				$storage->push($avatar->id, $squarePath, $square)
				) {

				$avatar->storage = $storageType;

				// Delete all the files now
				if ($this->deleteable()) {
					JFile::delete($smallPath);
					JFile::delete($mediumPath);
					JFile::delete($largePath);
					JFile::delete($squarePath);
				}

				$avatar->store();

				$success = true;
			}

			// Add this to the storage logs
			$this->log($avatar->id, 'avatars', $success);

			$total += 1;
		}

		if ($total > 0) {
			return JText::sprintf('%1s avatars uploaded to remote storage', $total);
		}
	}
}
