<?php
/**
 * @author Alexandre de Freitas Caetano <alexandrefc2@hotmail.com>
 */
namespace Vivapets\Moloni\Api;

/**
 * @TODO: Create an admin view and store these credentials in database instead of hardcoded
 */
interface CredentialsInterface
{
    /**
     * @var string
     */
    const MOLONI_CREDENTIALS_CLIENTID = '<your client_id here>';

    /**
     * @var string
     */
    const MOLONI_CREDENTIALS_EMAIL = '<your email here>'

    /**
     * @var string
     */
    const MOLONI_CREDENTIALS_PASSWORD = '<your password here>';

    /**
     * @var string
     */
    const MOLONI_CREDENTIALS_SECRET = '<your secret here>';

    /**
     * @var int
     */
    const MOLONI_CREDENTIALS_COMPANYID = '<your company_id here>';
}
