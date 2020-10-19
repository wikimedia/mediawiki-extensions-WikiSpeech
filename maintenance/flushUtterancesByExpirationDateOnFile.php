<?php

namespace MediaWiki\Wikispeech;

use Maintenance;

use MediaWiki\Wikispeech\Utterance\FlushUtterancesByExpirationDateOnFileJobQueue;
use MediaWiki\Wikispeech\Utterance\UtteranceStore;

/**
 * @file
 * @ingroup Extensions
 * @license GPL-2.0-or-later
 */

$IP = getenv( 'MW_INSTALL_PATH' );
if ( $IP === false ) {
	$IP = __DIR__ . '/../../..';
}
require_once "$IP/maintenance/Maintenance.php";

/**
 * Maintenance script to manually execute
 * {@link UtteranceStore::flushUtterancesByExpirationDateOnFile()}.
 * Used to clear out orphaned files (i.e. not tracked by utterance database).
 *
 * php extensions/Wikispeech/maintenance/flushUtterancesByExpirationDateOnFile.php
 *
 * @since 0.1.7
 */
class FlushUtterancesByExpirationDateOnFile extends Maintenance {

	/** @var UtteranceStore */
	private $utteranceStore;

	/** @var FlushUtterancesByExpirationDateOnFileJobQueue */
	private $jobQueue;

	public function __construct() {
		parent::__construct();
		$this->requireExtension( 'Wikispeech' );
		$this->addDescription( 'Flush orphaned utterances from file backend.' );
		$this->addOption(
			'force',
			'Forces flushing in current thread rather than queuing as job.',
			false,
			false,
			'f'
		);
	}

	/**
	 * @return bool success
	 */
	public function execute() {
		// Non PHP core classes aren't available prior to this point,
		// i.e. we can't initialize the fields in the constructor,
		// and we have to be lenient for mocked instances set by tests.
		if ( !$this->utteranceStore ) {
			$this->utteranceStore = new UtteranceStore();
		}
		if ( !$this->jobQueue ) {
			$this->jobQueue = new FlushUtterancesByExpirationDateOnFileJobQueue();
		}

		$force = $this->hasOption( 'force' );
		if ( $force ) {
			$this->utteranceStore->flushUtterancesByExpirationDateOnFile();
		} else {
			$this->jobQueue->queueJob();
		}
		return true;
	}

}

$maintClass = FlushUtterancesByExpirationDateOnFile::class;

require_once RUN_MAINTENANCE_IF_MAIN;
