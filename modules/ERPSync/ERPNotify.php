<?php

class ERPNotify {

    function createAccount($bean, $updateDescripton){}

    function updateAccount($bean, $updateDescripton, $obsolete = false){}

    function createContact($bean){}

    function updateContact($bean){}

    function addContactToAccount($contact, $account){}

    function removeContactFromAccount($contact, $account){}

    function deleteAccount($bean){ return true;}

    function deleteContact($bean){ return true;}

    function accountRequiredFields() {return array();}

    function account_pre_save($bean){}

}