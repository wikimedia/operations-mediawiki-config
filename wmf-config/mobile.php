<?php

# WARNING: This file is publically viewable on the web.
# # Do not put private data here.

if( $cluster === 'pmtpa' ) {  # safeguard

if ( $wmgMobileFrontend ) {
	require_once( "$IP/extensions/MobileFrontend/MobileFrontend.php" );
	$wgMFRemotePostFeedbackUsername = $wmgMFRemotePostFeedbackUsername;
	$wgMFRemotePostFeedbackPassword = $wmgMFRemotePostFeedbackPassword;
	$wgMFRemotePostFeedback = true;
	$wgMFRemotePostFeedbackUrl = "http://www.mediawiki.org/w/api.php";
	$wgMFRemotePostFeedbackArticle = "Project:Mobile site feedback";
	$wgMFFeedbackFallbackURL = '//en.m.wikipedia.org/wiki/Wikipedia:Contact_us';

	$wgHooks['MobileFrontendOverrideFeedbackLinks'][] = 'MobileFrontendFeedbackConfig';
	function MobileFrontendFeedbackConfig() {
		global $wgLanguageCode, $wgDBname, $wgMFFeedbackLinks;

		$infoEmails = array(
			'af',
			'ar',
			'ca',
			'cs',
			'da',
			'de',
			'el',
			//'en', Ommitted on purpose
			'es',
			'et',
			'fa',
			'fi',
			'fr',
			'he',
			'hi',
			'hr',
			'hu',
			'it',
			'ja',
			'ko',
			'ml',
			'nds',
			'nl',
			'no',
			'pl',
			'pt',
			'ro',
			'ru',
			'sk',
			'sr',
			'sv',
			'tr',
			'vi',
			'zh',
		);

		$lang = ( in_array( $wgLanguageCode, $infoEmails ) ) ? $wgLanguageCode : 'en';
		$msgOpts = array( 'language' => $lang );
		/** Get email subjects **/
		$subjectPreface = "[Mobile feedback] ";
		$generalSubject = $subjectPreface . wfMsgExt( 'mobile-frontend-leave-feedback-general-link-text', $msgOpts );
		$articlePersonalSubject = $subjectPreface . wfMsgExt( 'mobile-frontend-leave-feedback-article-personal-link-text', $msgOpts );
		$articleFactualSubject = $subjectPreface . wfMsgExt( 'mobile-frontend-leave-feedback-article-factual-link-text', $msgOpts );
		$articleOtherSubject = $subjectPreface . wfMsgExt( 'mobile-frontend-leave-feedback-article-other-link-text', $msgOpts );
		$technicalSubject = $subjectPreface . wfMsgExt( 'mobile-frontend-leave-feedback-technical-link-text', $msgOpts );

		/** Build links **/
		$emailStub = "info-$lang";

		// factual error link
		if ( $wgDBname == 'enwiki' ) {
			$articleFactualLink = "//en.m.wikipedia.org/wiki/Wikipedia:Contact_us/Article_problem/Factual_error";
		} else {
			$articleFactualLink = "mailto:$emailStub@wikimedia.org?subject=$articleFactualSubject";
		}

		// all other links - only en uses suffix routing
		$generalLink = "mailto:$emailStub@wikimedia.org?subject=$generalSubject";
		if ( $lang == 'en' ) {
			$articlePersonalLink = "mailto:$emailStub-q@wikimedia.org?subject=$articlePersonalSubject";
			$articleOtherLink = "mailto:$emailStub-o@wikimedia.org?subject=$articleOtherSubject";
		} else {
			$articlePersonalLink = "mailto:$emailStub@wikimedia.org?subject=$articlePersonalSubject";
			$articleOtherLink = "mailto:$emailStub@wikimedia.org?subject=$articleOtherSubject";
		}

		$technicalBody = wfMsgExt( 'mobile-frontend-leave-feedback-email-body', $msgOpts ) . "\nUser-agent: " . $_SERVER['HTTP_USER_AGENT'] . "\n";
		$technicalLink = "mailto:feedbacktest@wikimedia.org?subject=$technicalSubject&body=$technicalBody";

		$wgMFFeedbackLinks = array(
			'Technical' => $technicalLink, // Technical feedback
			'General' => $generalLink, // General feedback
			'ArticlePersonal' => $articlePersonalLink, // Regarding me, a person, or a company I work for
			'ArticleFactual' => $articleFactualLink, // Regarding a factual error
			'ArticleOther' => $articleOtherLink, // Regarding another problem
		);
		return true;
	}  # MobileFrontendFeedbackConfig()

	if ( $wmgMobileFrontendLogo ) {
		$wgMobileFrontendLogo = $wmgMobileFrontendLogo;
	}
	if ( $wmgMFRemovableClasses ) {
		$wgMFRemovableClasses = $wmgMFRemovableClasses;
	}
	if ( $wmgMFCustomLogos ) {
		if ( isset( $wmgMFCustomLogos['copyright'] ) ) {
			$wmgMFCustomLogos['copyright'] = str_replace( '{wgExtensionAssetsPath}', $wgExtensionAssetsPath, $wmgMFCustomLogos['copyright'] );
		}
		$wgMFCustomLogos = $wmgMFCustomLogos;
	}
}


// If a URL template is set for MobileFrontend, use it.
if ( $wmgMobileUrlTemplate ) {
	$wgMobileUrlTemplate = $wmgMobileUrlTemplate;
}

if ( $wmgZeroRatedMobileAccess ) {
	require_once( "$IP/extensions/ZeroRatedMobileAccess/ZeroRatedMobileAccess.php" );
}

if ( $wmgZeroDisableImages ) {
	if ( isset( $_SERVER['HTTP_X_SUBDOMAIN'] ) && strtoupper( $_SERVER['HTTP_X_SUBDOMAIN'] ) == 'ZERO' ) {
		$wgZeroDisableImages = $wmgZeroDisableImages;
	}
}

// Log WLM app API errors
// Standard exception log does not log UsageException and does not filter by user-agent
$wgDebugLogGroups['wlm-api'] = "udp://$wmfUdp2logDest/wlm-api";
$wgHooks['ApiMain::onException'][] = function( $apiMain, $e ) {
	$userAgent = $apiMain->getRequest()->getHeader( 'User-Agent' );
	if ( $userAgent && strpos( $userAgent, 'WLMMobile' ) !== false ) {
		wfDebugLog( 'wlm-api', $e->getLogMessage() . "\n\tUser-agent: $userAgent" );
	}
	return true;
};


} # safeguard
