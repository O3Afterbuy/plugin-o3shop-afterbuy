<?php
 /*
 * @package Afterbuy O3 Shop
 * @copyright (C) VIA-Online GmbH
 * 
 * This Software is the property of VIA-Online GmbH
 * and is protected by copyright law.
 */
/**
 * Metadata version
 */
$sMetadataVersion = '1.2';

/**
 * Module information
 */
$aModule = array(
    'id'           => 'o3shop',
    'title'        => 'Afterbuy Connector',
    'description'  => array(
        'de'=>'Schnittstelle zur Afterbuy API',
        'en'=>'Interface to Afterbuy API',
    ),
    'thumbnail'    => '',
    'version'      => '1.0.0',
    'author'       => 'VIA-Online',
    'url'          => 'https://www.afterbuy.de/',
    'email'        => 'support@afterbuy.de',
    'extend'       => array(
        // models
        'oxarticle'         => 'via/o3shop/extend/application/models/fcafterbuy_oxarticle',
        'oxuser'            => 'via/o3shop/extend/application/models/fcafterbuy_oxuser',
        'oxcategory'        => 'via/o3shop/extend/application/models/fcafterbuy_oxcategory',
        'oxorder'           => 'via/o3shop/extend/application/models/fcafterbuy_oxorder',
        'oxcounter'         => 'via/o3shop/extend/application/models/fcafterbuy_oxcounter',
    ),
    'files' => array(
        // core
        'fcafterbuyaddress'                 => 'via/o3shop/core/afterbuy/fcafterbuyaddress.php',
        'fcafterbuyapi'                     => 'via/o3shop/core/afterbuy/fcafterbuyapi.php',
        'fcafterbuyart'                     => 'via/o3shop/core/afterbuy/fcafterbuyart.php',
        'fcafterbuyorder'                   => 'via/o3shop/core/afterbuy/fcafterbuyorder.php',
        'fcafterbuyorderstatus'             => 'via/o3shop/core/afterbuy/fcafterbuyorderstatus.php',
        'fcafterbuypayment'                 => 'via/o3shop/core/afterbuy/fcafterbuypayment.php',
        'fcafterbuyshipping'                => 'via/o3shop/core/afterbuy/fcafterbuyshipping.php',
        'fcafterbuysolditem'                => 'via/o3shop/core/afterbuy/fcafterbuysolditem.php',
        'fcafterbuyaddcatalog'              => 'via/o3shop/core/afterbuy/fcafterbuyaddcatalog.php',
        'fcafterbuyaddattribute'            => 'via/o3shop/core/afterbuy/fcafterbuyaddattribute.php',
        'fcafterbuyaddbaseproduct'          => 'via/o3shop/core/afterbuy/fcafterbuyaddbaseproduct.php',
        'fcafterbuybaseproductrelationdata' => 'via/o3shop/core/afterbuy/fcafterbuybaseproductrelationdata.php',
        'fcafterbuycatalog'                 => 'via/o3shop/core/afterbuy/fcafterbuycatalog.php',
        'fcafterbuyuseebayvariation'        => 'via/o3shop/core/afterbuy/fcafterbuyuseebayvariation.php',
        'fcafterbuyebayvariationvalue'      => 'via/o3shop/core/afterbuy/fcafterbuyebayvariationvalue.php',
        'fco2a_events'                      => 'via/o3shop/core/fco2a_events.php',
        'fco2adatabase'                     => 'via/o3shop/core/fco2adatabase.php',
        'fco2abase'                         => 'via/o3shop/core/fco2abase.php',
        'fco2abaseimport'                   => 'via/o3shop/core/fco2abaseimport.php',
        'fco2aorder'                        => 'via/o3shop/core/fco2aorder.php',
        'fco2aartexport'                    => 'via/o3shop/core/fco2aartexport.php',
        'fco2aorderimport'                  => 'via/o3shop/core/fco2aorderimport.php',
        'fco2astatusexport'                 => 'via/o3shop/core/fco2astatusexport.php',
        'fco2astatusimport'                 => 'via/o3shop/core/fco2astatusimport.php',
        'fco2aartimport'                    => 'via/o3shop/core/fco2aartimport.php',
        'fco2astockimport'                  => 'via/o3shop/core/fco2astockimport.php',
        'fco2alogger'                       => 'via/o3shop/core/fco2alogger.php',
        // controllers->admin
        'fcafterbuy_article_admin'          => 'via/o3shop/application/controllers/admin/fcafterbuy_article_admin.php',
        'fcafterbuy_admin'                  => 'via/o3shop/application/controllers/admin/fcafterbuy_admin.php',
        'fcafterbuy_list'                   => 'via/o3shop/application/controllers/admin/fcafterbuy_list.php',
        'fcafterbuy_payments'               => 'via/o3shop/application/controllers/admin/fcafterbuy_payments.php',
        'fcafterbuy_actions'                => 'via/o3shop/application/controllers/admin/fcafterbuy_actions.php',
        'fcafterbuy_orderinfo'              => 'via/o3shop/application/controllers/admin/fcafterbuy_orderinfo.php',
    ),
    'templates' => array(
        'fcafterbuy_article_admin.tpl'      => 'via/o3shop/application/views/admin/tpl/fcafterbuy_article_admin.tpl',
        'fcafterbuy_admin.tpl'              => 'via/o3shop/application/views/admin/tpl/fcafterbuy_admin.tpl',
        'fcafterbuy_list.tpl'               => 'via/o3shop/application/views/admin/tpl/fcafterbuy_list.tpl',
        'fcafterbuy_actions.tpl'            => 'via/o3shop/application/views/admin/tpl/fcafterbuy_actions.tpl',
        'fcafterbuy_payments.tpl'           => 'via/o3shop/application/views/admin/tpl/fcafterbuy_payments.tpl',
        'fcafterbuy_orderinfo.tpl'          => 'via/o3shop/application/views/admin/tpl/fcafterbuy_orderinfo.tpl',
    ),
    'blocks' => array(
    ),
    'events'        => array(
        'onActivate' => 'fco2a_events::onActivate',
        'onDeactivate' => 'fco2a_events::onDeactivate',
    ),
    'settings' => array(
        array(
            'group' => 'fcafterbuy_general',
            'name' => 'sFcAfterbuyLeadSystem',
            'type' => 'select',
            'value' => '0',
            'constraints' => '0|1'
        ),
        array(
            'group' => 'fcafterbuy_connect',
            'name' => 'blFcAfterbuyExportUTF8Orders',
            'type' => 'bool',
            'value' => true
        ),
        array(
            'group' => 'fcafterbuy_connect',
            'name' => 'sFcAfterbuyPartnerId',
            'type' => 'str',
            'value' => ""
        ),
        array(
            'group' => 'fcafterbuy_connect',
            'name' => 'sFcAfterbuyPartnerPassword',
            'type' => 'password',
            'value' => ""
        ),
        array(
            'group' => 'fcafterbuy_connect',
            'name' => 'sFcAfterbuyUsername',
            'type' => 'str',
            'value' => ""
        ),
        array(
            'group' => 'fcafterbuy_connect',
            'name' => 'sFcAfterbuyUserPassword',
            'type' => 'password',
            'value' => ""
        ),
        array(
            'group' => 'fcafterbuy_export',
            'name' => 'blFcAfterbuyExportAll',
            'type' => 'bool',
            'value' => false
        ),

        array(
            'group' => 'fcafterbuy_import',
            'name' => 'sFcAfterbuyImportArticleNumber',
            'type' => 'select',
            'value' => '0',
            'constraints' => '0|1|2|3'
        ),
        array(
            'group' => 'fcafterbuy_import',
            'name' => 'blFcAfterbuyIgnoreArticlesWithoutNr',
            'type' => 'bool',
            'value' => false
        ),
        array(
            'group' => 'fcafterbuy_order',
            'name' => 'blFcSendOrdersOnTheFly',
            'type' => 'bool',
            'value' => false
        ),
        array(
            'group' => 'fcafterbuy_order',
            'name' => 'sFcSendOrderNrInAdditionalField',
            'type' => 'select',
            'value' => '0',
            'constraints' => '0|1'
        ),
        array(
            'group' => 'fcafterbuy_order',
            'name' => 'aFcAfterbuyDebitPayments',
            'type' => 'arr',
            'value' => array(
                'oxiddebitnote'
            )
        ),
        array(
            'group' => 'fcafterbuy_order',
            'name' => 'aFcAfterbuyDebitDynBankname',
            'type' => 'aarr',
            'value' => array(
                'oxiddebitnote'=>'lsbankname'
            )
        ),
        array(
            'group' => 'fcafterbuy_order',
            'name' => 'aFcAfterbuyDebitDynBankzip',
            'type' => 'aarr',
            'value' => array(
                'oxiddebitnote'=>'lsblz'
            )
        ),
        array(
            'group' => 'fcafterbuy_order',
            'name' => 'aFcAfterbuyDebitDynAccountNr',
            'type' => 'aarr',
            'value' => array(
                'oxiddebitnote'=>'lsktonr'
            )
        ),
        array(
            'group' => 'fcafterbuy_order',
            'name' => 'aFcAfterbuyDebitDynAccountOwner',
            'type' => 'aarr',
            'value' => array(
                'oxiddebitnote'=>'lsktoinhaber'
            )
        ),
        array(
            'group' => 'fcafterbuy_order',
            'name' => 'sFcAfterbuyFeedbackType',
            'type' => 'select',
            'value' => '0',
            'constraints' => '0|1|2'
        ),
        array(
            'group' => 'fcafterbuy_order',
            'name' => 'sFcAfterbuyDeliveryCalculation',
            'type' => 'select',
            'value' => '1',
            'constraints' => '0|1'
        ),
        array(
            'group' => 'fcafterbuy_order',
            'name' => 'sFcAfterbuySendVat',
            'type' => 'select',
            'value' => '0',
            'constraints' => '0|1'
        ),
        array(
            'group' => 'fcafterbuy_order',
            'name' => 'blFcAfterbuyUseOwnCustNr',
            'type' => 'bool',
            'value' => false
        ),
        array(
            'group' => 'fcafterbuy_order',
            'name' => 'sFcAfterbuyCustIdent',
            'type' => 'select',
            'value' => '1',
            'constraints' => '0|1|2'
        ),
        array(
            'group' => 'fcafterbuy_order',
            'name' => 'sFcAfterbuyOverwriteEbayName',
            'type' => 'select',
            'value' => '1',
            'constraints' => '0|1'
        ),
        array(
            'group' => 'fcafterbuy_order',
            'name' => 'aFcAfterbuyPaymentsSetPaid',
            'type' => 'arr',
            'value' => array()
        ),
        array(
            'group' => 'fcafterbuy_order',
            'name' => 'sFcAfterbuyMarkId',
            'type' => 'str',
            'value' => ''
        ),
        array(
            'group' => 'fcafterbuy_order',
            'name' => 'aFcAfterbuyPayments',
            'type' => 'aarr',
            'value' => array(
                1=>'Überweisung',
                2=>'Bar/Abholung',
                4=>'Nachnahme',
                5=>'Paypal',
                6=>'Überweisung/Rechnung',
                7=>'Bankeinzug',
                9=>'Click&Buy',
                11=>'Expresskauf/Bonicheck',
                12=>'Sofortüberweisung',
                13=>'Nachnahme/Bonicheck',
                14=>'Ebay Express',
                15=>'Moneybookers',
                16=>'Kreditkarte',
                17=>'Lastschrift',
                18=>'Billsafe',
                19=>'Kreditkartenzahlung',
                20=>'Ideal',
                21=>'Carte Bleue',
                23=>'Onlineüberweisung',
                24=>'Giropay',
                25=>'Dankort',
                26=>'EPS',
                27=>'Przelewy24',
                28=>'Carta Si',
                29=>'Postepay',
                30=>'Nordea Solo Sweden',
                31=>'Nordea Solo Finland',
                34=>'Billsafe Ratenkauf',
            )
        ),
        array(
            'group' => 'fcafterbuy_order',
            'name' => 'sFcAfterbuySendWeight',
            'type' => 'select',
            'value' => '1',
            'constraints' => '0|1'
        ),
        array(
            'group' => 'fcafterbuy_debug',
            'name' => 'iFcAfterbuyLogLevel',
            'type' => 'select',
            'value' => '0',
            'constraints' => '0|1|2|3|4'
        ),
    )
);
