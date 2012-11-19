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
	function MobileFrontendFeedbackConfig( $feedbackSource, $referringArticle ) {
		global $wgLanguageCode, $wgDBname, $wgMFFeedbackLinks;

		/** Get email subjects **/
		$subjectPreface = "[Mobile feedback] ";
		$technicalSubject = $subjectPreface . wfMessage( 'mobile-frontend-leave-feedback-technical-link-text' )->inLanguage( $wgLanguageCode )->escaped();

		/** Build links **/
		$emailContext = "Source: " . $feedbackSource . "\nReferring page: " . $referringArticle . "\n";

		$technicalBody = wfMessage( 'mobile-frontend-leave-feedback-email-body' )->inLanguage( $wgLanguageCode )->escaped()
			. "\nUser-agent: " . $_SERVER['HTTP_USER_AGENT'] . "\n";
		$technicalBody .= $emailContext;
		$technicalLink = "mailto:mobile-feedback-l@lists.wikimedia.org?subject=$technicalSubject&body=$technicalBody";

		$wgMFFeedbackLinks = array(
			'Technical' => $technicalLink, // Technical feedback
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

// Enable loading of desktop-specific resources from MobileFrontend
if ( $wmgMFEnableDesktopResources ) {
	$wgMFEnableDesktopResources = true;
}

// Temporarily disable ResourceLoader integration in beta, as it's got some problems
$wgMFEnableResourceLoader = false;

// Enable appending of TM (text) / (R) (icon) on site name in footer.
// See bug 41141 though, we may wish to disable on some sites.
$wgMFTrademarkSitename = true;

} # safeguard
