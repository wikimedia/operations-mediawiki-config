<?php

$wgHooks['EmailUserForm'][] = function ( $form ) {
	$form->setWrapperLegend( "Apply to start a user group" );
	$form->setId( "affcom-contact-form" );

	$rp = new ReflectionProperty( 'HTMLForm', 'mSubmitCallback' );
	$rp->setAccessible( true );
	$submitCallback = $rp->getValue( $form );
	$form->setSubmitCallback( function ( $d, $form ) use ( $submitCallback ) {
		$d['Subject'] = "User group request: " . $d['GroupName'];
		return call_user_func( $submitCallback, $d, $form );
	} );
	return true;
};
class AffComUserGroupLogoRadioField extends HTMLRadioField {
	// Simply append text below the options.
	function formatOptions( $options, $value ) {
		return parent::formatOptions( $options, $value ) .
			wfMessage( 'contactpage-affcom-user-group-logo-wm-logo' )->parse();
	}
}
class AffComUserGroupRulesAgreementField extends HTMLCheckField {
	// Forces the input to be in the label column... Otherwise the form looks silly.
	function getInputHTML( $value ) {
		return '';
	}
	function getLabel() {
		return parent::getInputHTML( '' );
	}
}
$wgContactConfig['affcomusergroup'] = array(
	'RecipientUser' => 'Krenair',
	'SenderName' => 'Contact Form on ' . $wgSitename,
	'SenderEmail' => null,
	'RequireDetails' => true,
	'IncludeIP' => false,
	'AdditionalFields' => array(
		'GroupName' => array(
			'label-message' => 'contactpage-affcom-user-group-name-label',
			'contactpage-email-label' => 'Group name',
			'type' => 'text'
		),
		'GroupDescription' => array(
			'label-message' => 'contactpage-affcom-user-group-description-label',
			'contactpage-email-label' => 'Group description',
			'type' => 'textarea',
			'rows' => 10

		),
		'GroupWikiPage' => array(
			'label-message' => 'contactpage-affcom-user-group-wikipage-label',
			'contactpage-email-label' => 'Group wiki page',
			'type' => 'text'
		),
		'GroupLocation' => array(
			'label-message' => 'contactpage-affcom-user-group-location-label',
			'contactpage-email-label' => 'Group location',
			'type' => 'text'
		),
		'GroupLeaders' => array(
			'label-message' => 'contactpage-affcom-user-group-leaders-label',
			'contactpage-email-label' => 'Active Wikimedians',
			'type' => 'textarea',
			'rows' => 10
		),
		'GroupLogo' => array(
			'label-message' => 'contactpage-affcom-user-group-logo-label',
			'contactpage-email-label' => 'Logo',
			'class' => 'AffComUserGroupLogoRadioField',
			'options' => array(
				'<b>Wikimedia community logo</b>' => 'Wikimedia community',
				'<b>Wikipedia affiliate logo</b>' => 'Wikipedia affiliate'
			)
		),
		'Rules' => array(
			'label-message' => 'contactpage-affcom-user-group-rules-label',
			'type' => 'info',
		),
		'Terms' => array(
			'label-message' => 'contactpage-affcom-user-group-terms-label',
			'contactpage-email-label' => 'Terms',
			'class' => 'AffComUserGroupRulesAgreementField',
			'required' => true,
			'validation-callback' => function ( $value ) { return !!$value; }
		)
	)
);