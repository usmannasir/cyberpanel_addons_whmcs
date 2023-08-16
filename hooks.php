<?php
/**
 * WHMCS SDK Sample Addon Module Hooks File
 *
 * Hooks allow you to tie into events that occur within the WHMCS application.
 *
 * This allows you to execute your own code in addition to, or sometimes even
 * instead of that which WHMCS executes by default.
 *
 * @see https://developers.whmcs.com/hooks/
 *
 * @copyright Copyright (c) WHMCS Limited 2017
 * @license http://www.whmcs.com/license/ WHMCS Eula
 */

// Require any libraries needed for the module to function.
// require_once __DIR__ . '/path/to/library/loader.php';
//
// Also, perform any initialization required by the service's library.

use \WHMCS\Module\Addon\Setting as AddonSetting;

/**
 * Register a hook with WHMCS.
 *
 * This sample demonstrates triggering a service call when a change is made to
 * a client profile within WHMCS.
 *
 * For more information, please refer to https://developers.whmcs.com/hooks/
 *
 * add_hook(string $hookPointName, int $priority, string|array|Closure $function)
 */


add_hook('AfterModuleCreate', 1, function ($vars) {
    // Get the order ID from the hook vars

    $serviceId = $vars['params']['serviceid'];

    ###

    $modulevars = AddonSetting::module('cyberpanel')->pluck('value', 'setting');

    $partnerKey = $modulevars->get('Partner Key'); // Access the value of 'Partner Key' setting

    logModuleCall(
        'cyberpanel_license',
        __FUNCTION__,
        $modulevars,
        'variables that are configured' . $partnerKey,
        'variables that are configured' . $partnerKey
    );

    ###


    // Retrieve the service information to get the order ID
    $hostingInfo = \WHMCS\Database\Capsule::table('tblhosting')
        ->where('id', $serviceId)
        ->first();


    if ($hostingInfo) {
        // Access specific columns in the hostingInfo object
        $dedicatedip = $hostingInfo->dedicatedip;

        logModuleCall(
            'cyberpanel_license',
            __FUNCTION__,
            $hostingInfo,
            'dedicated ip found' . $dedicatedip,
            'dedicated ip found' . $dedicatedip
        );

        // Your custom code to run after a VPS is deployed
        $data = $vars; // This can be a variable or an array

        // Convert the variable or array to a string
        if (is_array($data)) {
            $dataString = json_encode($data); // Convert array to JSON string
        } else {
            $dataString = $data;
        }

        // Check if the dataString contains 'cyberpanel_license'
        if (strpos($dataString, 'cyberpanel_license') !== false) {

            $curl = curl_init();

            curl_setopt_array($curl, array(
                CURLOPT_URL => 'https://platform.cyberpersons.com/order/ResellerAPI',
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => '',
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 0,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => 'POST',
                CURLOPT_POSTFIELDS => '{
    "ipadress": "' . $dedicatedip . '",
    "billingcycle": 1,
    "AdonName": "all"
}',
                CURLOPT_HTTPHEADER => array(
                    'Content-Type: application/json',
                    'Authorization: Bearer ' . $partnerKey
                ),
            ));

            $response = curl_exec($curl);

            curl_close($curl);

            logModuleCall(
                'cyberpanel_license',
                __FUNCTION__,
                $vars,
                $response,
                $response
            );

        }

    } else {
        // Handle case where hosting information is not found
        logModuleCall(
            'cyberpanel_license',
            __FUNCTION__,
            $vars,
            'hosting account not found' . $serviceId,
            'hosting account not found' . $serviceId
        );
    }
});

add_hook('PreModuleTerminate', 1, function ($vars) {
    // Get the order ID from the hook vars

    $serviceId = $vars['params']['serviceid'];

    ###

    $modulevars = AddonSetting::module('cyberpanel')->pluck('value', 'setting');

    $partnerKey = $modulevars->get('Partner Key'); // Access the value of 'Partner Key' setting

    logModuleCall(
        'cyberpanel_license',
        __FUNCTION__,
        $modulevars,
        'in cancel variables that are configured' . $partnerKey,
        'in cancel variables that are configured' . $partnerKey
    );

    ###


    // Retrieve the service information to get the order ID
    $hostingInfo = \WHMCS\Database\Capsule::table('tblhosting')
        ->where('id', $serviceId)
        ->first();


    if ($hostingInfo) {
        // Access specific columns in the hostingInfo object
        $productId = $hostingInfo->packageid;
        $domain = $hostingInfo->domain;
        $dedicatedip = $hostingInfo->dedicatedip;

        sleep(5);

        logModuleCall(
            'cyberpanel_license',
            __FUNCTION__,
            $hostingInfo,
            'cancel dedicated ip found' . $dedicatedip,
            'cancel dedicated ip found' . $dedicatedip
        );

        // Your custom code to run after a VPS is deployed
        $data = $vars; // This can be a variable or an array

        // Convert the variable or array to a string
        if (is_array($data)) {
            $dataString = json_encode($data); // Convert array to JSON string
        } else {
            $dataString = $data;
        }

        $fileHandle = fopen('cyberpanel.txt', 'w');
        // Write the 'Partner Key' configuration value
        fwrite($fileHandle, $dataString);
        fclose($fileHandle);

        // Check if the dataString contains 'cyberpanel_license'
        if (strpos($dataString, 'cyberpanel_license') !== false) {


            $curl = curl_init();

            curl_setopt_array($curl, array(
                CURLOPT_URL => 'https://platform.cyberpersons.com/order/DeleteResellerAPI',
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => '',
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 0,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => 'POST',
                CURLOPT_POSTFIELDS => '{
    "ipadress": "' . $dedicatedip . '",
    "billingcycle": 1,
    "AdonName": "all"
}',
                CURLOPT_HTTPHEADER => array(
                    'Content-Type: application/json',
                    'Authorization: Bearer ' . $partnerKey
                ),
            ));

            $response = curl_exec($curl);

            curl_close($curl);

            logModuleCall(
                'cyberpanel_license',
                __FUNCTION__,
                $vars,
                $response,
                $response
            );

        }

        // Rest of your code goes here
        // ...

    } else {
        // Handle case where hosting information is not found
        logModuleCall(
            'cyberpanel_license',
            __FUNCTION__,
            $vars,
            'cancel hosting account not found' . $serviceId,
            ' cancel hosting account not found' . $serviceId
        );
    }
});

add_hook('OrderPaid', 1, function ($vars) {
    // Perform hook code here...
    // Get the order ID from the hook vars

    $orderID = $vars['orderId'];

    $response = localAPI('GetOrders', ['orderid' => $orderID]);

    if ($response['result'] === 'success' && isset($response['orders']['order'][0]['lineitems']['lineitem'][0]['relid'])) {
        $serviceID = $response['orders']['order'][0]['lineitems']['lineitem'][0]['relid'];
    } else {
        pass;
    }


    ###

    $modulevars = AddonSetting::module('cyberpanel')->pluck('value', 'setting');

    $partnerKey = $modulevars->get('Partner Key'); // Access the value of 'Partner Key' setting

    logModuleCall(
        'cyberpanel_license',
        __FUNCTION__,
        $vars,
        'order paid ' . $orderID . ' ' . $partnerKey,
        'order paid ' . $orderID . ' ' . $partnerKey
    );


    $customFieldName = 'CyberPanel IP Address'; // Replace with the actual custom field name

    // Get the fieldid from tblcustomfields based on fieldname
    $fieldID = \WHMCS\Database\Capsule::table('tblcustomfields')
        ->where('fieldname', $customFieldName)
        ->pluck('id')
        ->first();

    logModuleCall(
        'cyberpanel_license',
        __FUNCTION__,
        $vars,
        'field id ' . $fieldID ,
        'field id  ' . $fieldID
    );

    if ($fieldID !== null) {

        $customFieldValue = \WHMCS\Database\Capsule::table('tblcustomfieldsvalues')
            ->where('relid', $serviceID)
            ->where('fieldid', $fieldID)
            ->pluck('value')
            ->first();

        logModuleCall(
            'cyberpanel_license',
            __FUNCTION__,
            $vars,
            'custom field value before null ' . $customFieldValue,
            'custom field value before null ' . $customFieldValue
        );

        if ($customFieldValue !== null) {
            // Use $customFieldValue in your code
            logModuleCall(
                'cyberpanel_license',
                __FUNCTION__,
                $vars,
                'custom field value ' . $customFieldValue,
                'custom field value ' . $customFieldValue
            );

            $curl = curl_init();

            curl_setopt_array($curl, array(
                CURLOPT_URL => 'https://platform.cyberpersons.com/order/ResellerAPI',
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => '',
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 0,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => 'POST',
                CURLOPT_POSTFIELDS => '{
    "ipadress": "' . $customFieldValue . '",
    "billingcycle": 1,
    "AdonName": "all"
}',
                CURLOPT_HTTPHEADER => array(
                    'Content-Type: application/json',
                    'Authorization: Bearer ' . $partnerKey
                ),
            ));

            $response = curl_exec($curl);

            curl_close($curl);

            logModuleCall(
                'cyberpanel_license',
                __FUNCTION__,
                $vars,
                $response,
                $response
            );


        } else {

            logModuleCall(
                'cyberpanel_license',
                __FUNCTION__,
                $vars,
                "Custom Field not found or value is empty",
                "Custom Field not found or value is empty"
            );
        }

    }


    ###
});
