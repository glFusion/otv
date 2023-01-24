<?php
/**
 * Class to handle secret creation and display.
 *
 * @author      Lee Garner <lee@leegarner.com>
 * @copyright   Copyright (c) 2022 Lee Garner <lee@leegarner.com>
 * @package     keyshare
 * @version     v0.0.1
 * @license     http://opensource.org/licenses/gpl-2.0.php
 *              GNU Public License v2 or later
 * @filesource
 */
namespace KeyShare\Models;
use glFusion\Database\Database;
use glFusion\Log\Log;
use KeyShare\Config;


/**
 * Class for secret creation and display.
 * @package keyshare
 */
class Secret
{
    /** Database record ID.
     * @var integer */
    private $id = 0;

    /** Timestamp when secret is created.
     * @var integer */
    private $ts = 0;

    /** Public encryption key.
     * @var string */
    private $pub_key = '';

    /** Secret value, unencrypted.
     * @var string */
    private $value = '';

    /** Private encryption key. Not stored anywhere.
     * @var string */
    private $_prv_key = '';

    /** Secret value, encrypted.
     * @var string */
    private $_enc_value = '';

    /** Errors accumulated during secret creation.
     * @var array */
    private $_errors = array();


    /**
     * If `id` is set, read the requested record. If `prv_key` is set use it.
     */
    public function __construct(?int $id=NULL, ?string $prv_key=NULL)
    {
        global $_TABLES;

        if ($prv_key) {
            $this->_prv_key = $prv_key;
        }

        if ($id > 0) {
            try {
                $row = Database::getInstance()->conn->executeQuery(
                    "SELECT * FROM {$_TABLES['keyshare_secrets']} WHERE id = ?",
                    array($id),
                    array(Database::INTEGER)
                )->fetchAssociative();
            } catch (\Throwable $e) {
                Log::write('system', Log::ERROR, __METHOD__ . ': ' . $e->getMessage());
                $row = false;
            }
            if (is_array($row)) {
                $this->id = (int)$row['id'];
                $this->ts = (int)$row['ts'];
                $this->pub_key = $row['pub_key'];
                $this->_enc_value = $row['value'];
                if (!empty($this->_prv_key)) {
                    // If a private key is set, decrypt the secret.
                    // Otherwise assume it will be set and decrypted later.
                    $this->value = $this->decrypt();
                }
            }
        }
    }


    /**
     * Set the record ID.
     *
     * @param   integer $id     DB record ID
     * @return  object  $this
     */
    public function withId(int $id) : self
    {
        $this->id = $id;
        return $this;
    }


    /**
     * Get the record ID.
     *
     * @return  integer     DB record ID
     */
    public function getId() : int
    {
        return $this->id;
    }


    /**
     * Set the timestamp.
     *
     * @param   integer $ts     Timestamp in seconds
     * @return  object  $this
     */
    public function withTs(int $ts) : self
    {
        $this->ts = $ts;
        return $this;
    }


    /**
     * Get the timestamp when the secret was saved.
     *
     * @return  integer     Timestamp value
     */
    public function getTs() : int
    {
        return $this->ts;
    }


    /**
     * Set the secret value.
     *
     * @param   string  $val    Secret string
     * @return  object  $this
     */
    public function withValue(string $val) : self
    {
        $this->value = $val;
        return $this;
    }


    /**
     * Get the secret value.
     *
     * @return  string      Secret string
     */
    public function getValue() : string
    {
        return $this->value;
    }


    /**
     * Set the public key value.
     *
     * @param   string  $key    Public key
     * @return  object  $this
     */
    public function withPubKey(string $key) : self
    {
        $this->pub_key = $key;
        return $this;
    }


    /**
     * Get the public key.
     *
     * @return  string      Public key
     */
    public function getPubKey() : string
    {
        return $this->pub_key;
    }


    /**
     * Decrypt the encrypted secret string.
     * Also sets the `value` property for later use.
     *
     * @return  string      Decrypted secret value
     */
    public function decrypt() : string
    {
        if (!empty($this->_prv_key) && !empty($this->pub_key)) {
            $this->value = COM_decrypt($this->_enc_value, $this->_prv_key . $this->pub_key);
        }
        return $this->value;
    }


    /**
     * Save the secret to the database.
     * Creates a key pair and encrypts the secret.
     *
     * @param   string  $value  Secret value, required if not set previously
     * @return  boolean     True on success, False on error
     */
    public function save(?string $value=NULL) : bool
    {
        global $_TABLES, $LANG_KEYSHARE;

        $this->_prv_key = Token::create();
        $this->pub_key = Token::create();
        if ($value !== NULL) {
            // should be already set
            $this->withValue($value);
        }
        if (empty($this->value)) {
            $this->_errors[] = $LANG_KEYSHARE['err_empty_value'];
            return false;
        }
        $this->_enc_value = COM_encrypt($this->value, $this->_prv_key . $this->pub_key);
        $db = Database::getInstance();
        try {
            $db->conn->insert(
                $_TABLES['keyshare_secrets'],
                array(
                    'ts' => time(),
                    'pub_key' => $this->pub_key,
                    'value' => $this->_enc_value,
                ),
                array(
                    Database::INTEGER,
                    Database::STRING,
                    Database::STRING,
                )
            );
            $this->id = $db->conn->lastInsertId();
        } catch (\Throwable $e) {
            Log::write('system', Log::ERROR, __METHOD__ . ': ' . $e->getMessage());
            $this->_errors[] = $LANG_KEYSHARE['op_failure'];
            $this->id = 0;
        }
        return $this->id > 0;
    }


    /**
     * Delete the current secret.
     * Normally called from render().
     */
    public function delete() : void
    {
        global $_TABLES;

        if ($this->id > 0) {
            try {
                Database::getInstance()->conn->delete(
                    $_TABLES['keyshare_secrets'],
                    array('id' => $this->id),
                    array(Database::INTEGER)
                );
            } catch (\Throwable $e) {
                Log::write('system', Log::ERROR, __METHOD__ . ': ' . $e->getMessage());
                $this->id = 0;
            }
        }
    }


    /**
     * Get the URL key to access the secret.
     * This is the encrypted record ID and private key using the standard
     * encryption salt.
     * This value can be created only once.
     *
     * @return  string      Encrypted version of id:prv_key
     */
    public function getUrlKey() : string
    {
        return COM_encrypt($this->id . ':' . $this->_prv_key);
    }


    /**
     * Get a secret object from the encrypted URL key.
     *
     * @param   string  $enc_key    Encrypted URL key
     * @return  object      New Secret object
     */
    public static function getFromUrlKey(string $enc_key) : ?self
    {
        $key_str = COM_decrypt($enc_key);
        if (!$key_str || strpos($key_str, ':') < 1) {
            // Invalid encrypted string
            return NULL;
        }
        list($id, $prv_key) = explode(':', $key_str);
        return new self($id, $prv_key);
    }


    /**
     * Expire stale secrets based on the configured number of days.
     *
     * @return  boolean     True on success, False on error
     */
    public static function expireStale() : bool
    {
        global $_TABLES;

        if (Config::get('expire_after') < 1) {
            // Not configured to expire, fake success.
            return true;
        }

        try {
            Database::getInstance()->conn->executeStatement(
                "DELETE FROM {$_TABLES['keyshare_secrets']} WHERE ts < ?",
                array(Config::get('expires_after', 7) * 86400),
                array(Database::INTEGER)
            );
            return true;
        } catch (\Throwable $e) {
            Log::write('system', Log::ERROR, __METHOD__ . ': ' . $e->getMessage());
            return false;
        }
    }


    /**
     * Purge all secrets. Called from the admin console.
     *
     * @return  boolean     True on success, False on error
     */
    public static function purge() : bool
    {
        global $_TABLES;

        try {
            Database::getInstance()->conn->delete(
                $_TABLES['keyshare_secrets'],
                array(1 => 1),
                array(Database::INTEGER)
            );
            return true;
        } catch (\Throwable $e) {
            Log::write('system', Log::ERROR, __METHOD__ . ': ' . $e->getMessage());
            return false;
        }
    }


    /**
     * Create the submission form.
     *
     * @return  string      HTML for submission form
     */
    public static function submit() : string
    {
        $T = new \Template(Config::get('template_path'));
        $T->set_file('form', 'submit.thtml');
        PLG_templateSetVars('keyshare', $T);
        $T->parse('output', 'form');
        return $T->finish($T->get_var('output'));
    }


    /**
     * Display the secret.
     *
     * @return  string      Secret display form
     */
    public function render() : string
    {
        $T = new \Template(Config::get('template_path'));
        $T->set_file('display', 'display.thtml');
        $T->set_var('secret', htmlspecialchars($this->getValue()));
        $T->parse('output', 'display');
        if (Config::get('del_after_view')) {
            $this->delete();
        }
        return $T->finish($T->get_var('output'));
    }


    /**
     * Get any errors that occurred.
     * Returns either a formatted un-numbered list, or a raw array of strings
     * depending on the parameter value.
     *
     * @param   boolean $format     True for a formatted list, False for an array
     * @return  string|array    Formatted list or array
     */
    public function getErrors(bool $format=false)
    {
        if ($format) {
           if (!empty($this->_errors)) {
               return '<ul><li>' . implode('</li><li>', $this->_errors) . '</li></ul>';
           } else {
               return '';
           }
        } else {
            return $this->_errors;
        }
    }

}
