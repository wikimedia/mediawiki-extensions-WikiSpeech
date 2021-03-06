<?php

namespace MediaWiki\Wikispeech\Hooks;

/**
 * @file
 * @ingroup Extensions
 * @license GPL-2.0-or-later
 */

use DatabaseUpdater;
use MediaWiki\Installer\Hook\LoadExtensionSchemaUpdatesHook;

/**
 * @since 0.1.8
 */
class DatabaseHooks
	implements LoadExtensionSchemaUpdatesHook {
	/**
	 * Creates database tables.
	 *
	 * @param DatabaseUpdater $updater
	 * @since 0.1.8
	 */
	public function onLoadExtensionSchemaUpdates( $updater ) {
		$updater->addExtensionTable(
			'wikispeech_utterance',
			dirname( __DIR__ ) . "/../sql/{$updater->getDB()->getType()}/tables-generated.sql"
		);
	}
}
