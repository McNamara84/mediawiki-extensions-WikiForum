<?php
/**
 * @file
 * @ingroup Maintenance
 */
$IP = getenv( 'MW_INSTALL_PATH' );
if ( $IP === false ) {
	$IP = __DIR__ . '/../../..';
}
require_once "$IP/maintenance/Maintenance.php";

/**
 * Run automatically with update.php
 *
 * - Adds new actor columns to the required tables
 * - Populates these columns appropriately
 * - And finally drops the said columns
 *
 * @since January 2020
 */
class MigrateOldWikiForumUserColumnsToActor extends LoggedUpdateMaintenance {
	public function __construct() {
		parent::__construct();
		$this->addDescription( 'Migrates data from old user ID columns in WikiForum database tables to the new actor columns.' );
	}

	/**
	 * Get the update key name to go in the update log table
	 *
	 * @return string
	 */
	protected function getUpdateKey() {
		return __CLASS__;
	}

	/**
	 * Message to show that the update was done already and was just skipped
	 *
	 * @return string
	 */
	protected function updateSkippedMessage() {
		return 'WikiForum\'s database tables have already been migrated to use the actor columns.';
	}

	/**
	 * Do the actual work.
	 *
	 * @return bool True to log the update as done
	 */
	protected function doDBUpdates() {
		$dbw = $this->getDB( DB_MASTER );

		// wikiforum_category
		$res = $dbw->select(
			'wikiforum_category',
			[
				'wfc_added_user',
				'wfc_edited_user',
				'wfc_deleted_user',
			],
			'',
			__METHOD__,
			[ 'DISTINCT' ]
		);
		foreach ( $res as $row ) {
			$user = $this->getUser( $row->wfc_added_user );
			if ( $user ) {
				$dbw->update(
					'wikiforum_category',
					[
						'wfc_added_actor' => (int)$user->getActorId( $dbw )
					],
					[
						'wfc_added_user' => (int)$row->wfc_added_user
					],
					__METHOD__
				);
			}

			$user = $this->getUser( $row->wfc_edited_user );
			if ( $user ) {
				$dbw->update(
					'wikiforum_category',
					[
						'wfc_edited_actor' => (int)$user->getActorId( $dbw )
					],
					[
						'wfc_edited_user' => (int)$row->wfc_edited_user
					],
					__METHOD__
				);
			}

			$user = $this->getUser( $row->wfc_deleted_user );
			if ( $user ) {
				$dbw->update(
					'wikiforum_category',
					[
						'wfc_deleted_actor' => (int)$user->getActorId( $dbw )
					],
					[
						'wfc_deleted_user' => (int)$row->wfc_deleted_user
					],
					__METHOD__
				);
			}
		}

		// wikiforum_forums
		$res = $dbw->select(
			'wikiforum_forums',
			[
				'wff_last_post_user',
				'wff_added_user',
				'wff_edited_user',
				'wff_deleted_user'
			],
			'',
			__METHOD__,
			[ 'DISTINCT' ]
		);
		foreach ( $res as $row ) {
			$user = $this->getUser( $row->wff_last_post_user );
			if ( $user ) {
				$dbw->update(
					'wikiforum_forums',
					[
						'wff_last_post_actor' => (int)$user->getActorId( $dbw )
					],
					[
						'wff_last_post_user' => (int)$row->wff_last_post_user
					],
					__METHOD__
				);
			}

			$user = $this->getUser( $row->wff_added_user );
			if ( $user ) {
				$dbw->update(
					'wikiforum_forums',
					[
						'wff_added_actor' => (int)$user->getActorId( $dbw )
					],
					[
						'wff_added_user' => (int)$row->wff_added_user
					],
					__METHOD__
				);
			}

			$user = $this->getUser( $row->wff_edited_user );
			if ( $user ) {
				$dbw->update(
					'wikiforum_forums',
					[
						'wff_edited_actor' => (int)$user->getActorId( $dbw )
					],
					[
						'wff_edited_user' => (int)$row->wff_edited_user
					]
				);
			}

			$user = $this->getUser( $row->wff_deleted_user );
			if ( $user ) {
				$dbw->update(
					'wikiforum_forums',
					[
						'wff_deleted_actor' => (int)$user->getActorId( $dbw )
					],
					[
						'wff_deleted_user' => (int)$row->wff_deleted_user
					],
					__METHOD__
				);
			}
		}

		// wikiforum_threads
		$res = $dbw->select(
			'wikiforum_threads',
			[
				'wft_user',
				'wft_deleted_user',
				'wft_edit_user',
				'wft_closed_user',
				'wft_last_post_user'
			],
			'',
			__METHOD__,
			[ 'DISTINCT' ]
		);
		foreach ( $res as $row ) {
			$user = $this->getUser( $row->wft_user );
			if ( $user ) {
				$dbw->update(
					'wikiforum_threads',
					[
						'wft_actor' => (int)$user->getActorId( $dbw )
					],
					[
						'wft_user' => (int)$row->wft_user
					],
					__METHOD__
				);
			}

			$user = $this->getUser( $row->wft_deleted_user );
			if ( $user ) {
				$dbw->update(
					'wikiforum_threads',
					[
						'wft_deleted_actor' => (int)$user->getActorId( $dbw )
					],
					[
						'wft_deleted_user' => (int)$row->wft_deleted_user
					],
					__METHOD__
				);
			}

			$user = $this->getUser( $row->wft_edit_user );
			if ( $user ) {
				$dbw->update(
					'wikiforum_threads',
					[
						'wft_edit_actor' => (int)$user->getActorId( $dbw )
					],
					[
						'wft_edit_user' => (int)$row->wft_edit_user
					],
					__METHOD__
				);
			}

			$user = $this->getUser( $row->wft_closed_user );
			if ( $user ) {
				$dbw->update(
					'wikiforum_threads',
					[
						'wft_closed_actor' => (int)$user->getActorId( $dbw )
					],
					[
						'wft_closed_user' => (int)$row->wft_closed_user
					],
					__METHOD__
				);
			}

			$user = $this->getUser( $row->wft_last_post_user );
			if ( $user ) {
				$dbw->update(
					'wikiforum_threads',
					[
						'wft_last_post_actor' => (int)$user->getActorId( $dbw )
					],
					[
						'wft_last_post_user' => (int)$row->wft_last_post_user
					],
					__METHOD__
				);
			}
		}

		// wikiforum_replies
		$res = $dbw->select(
			'wikiforum_replies',
			[
				'wfr_user',
				'wfr_deleted_user',
				'wfr_edit_user'
			],
			'',
			__METHOD__,
			[ 'DISTINCT' ]
		);
		foreach ( $res as $row ) {
			$user = $this->getUser( $row->wfr_user );
			if ( $user ) {
				$dbw->update(
					'wikiforum_replies',
					[
						'wfr_actor' => (int)$user->getActorId( $dbw )
					],
					[
						'wfr_user' => (int)$row->wfr_user
					],
					__METHOD__
				);
			}

			$user = $this->getUser( $row->wfr_deleted_user );
			if ( $user ) {
				$dbw->update(
					'wikiforum_replies',
					[
						'wfr_deleted_actor' => (int)$user->getActorId( $dbw )
					],
					[
						'wfr_deleted_user' => (int)$row->wfr_deleted_user
					],
					__METHOD__
				);
			}

			$user = $this->getUser( $row->wfr_edit_user );
			if ( $user ) {
				$dbw->update(
					'wikiforum_replies',
					[
						'wfr_edit_actor' => (int)$user->getActorId( $dbw )
					],
					[
						'wfr_edit_user' => (int)$row->wfr_edit_user
					],
					__METHOD__
				);
			}
		}

		return true;
	}

	/**
	 * Fetches the user from newFromId.
	 *
	 * @param string $userId
	 *
	 * @return User|false
	 */
	protected function getUser( $userId ) {
		if ( (int)$userId === 0 ) {
			return false;
		}

		// We create a user object
		// to get to actor id.
		return User::newFromId( $userId );
	}
}

$maintClass = MigrateOldWikiForumUserColumnsToActor::class;
require_once RUN_MAINTENANCE_IF_MAIN;
