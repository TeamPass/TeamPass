/**
 * NOTICE OF LICENSE
 *
 * This source file is subject to version 3 of the GPL license,
 * that is bundled with this package in the file LICENSE, and is
 * available online at http://www.gnu.org/licenses/gpl.txt
 *
 * @author    Philipp Dittert <philipp.dittert@gmail.com>
 * @copyright 2020 Philipp Dittert <philipp.dittert@gmail.com>
 * @license   http://www.gnu.org/licenses/gpl.txt GNU General Public License, version 3 (GPL-3.0)
 * @link      https://github.com/TeamPass/TeamPass
 */

Ext.define('TeamPass.Locales', {
    singleton: true,

    constructor: function(config) {
        this.loadCurrentLanguageFromLocalStorage();
        return this;
    },

    gettext: function(textId, params) {

        var value;

        if (!this.data[textId]) {
            return "#" + textId;
        }

        if (this.data[textId][this.language]) {
            value = this.data[textId][this.language];
            for (var k in params) {
                value = value.replace("{"+k+"}", params[k]);
            }
            return value;
        } else {
            return "#" + textId;
        }
    },

    getCurrentLanguage: function() {
        return this.language;
    },

    loadCurrentLanguageFromLocalStorage: function() {
        currentLocale = localStorage.getItem('currentLocale');
        if (currentLocale) {
            this.language = currentLocale;
        } else {
            this.language = TeamPass.Util.Settings.get("defaultLocale");
        }
    },

    setLanguage: function(lang) {
        if (lang === null) {
            lang = TeamPass.Util.Settings.get("defaultLocale");
        }

        localStorage.setItem('currentLocale', lang);
        this.language = lang;
    },

    data: {
        "LOGIN.HEADLINE": {
            "en": "TeamPass-Login",
            "de": "TeamPass-Login"
        },
        "LOGIN.USERNAME": {
            "en": "Username",
            "de": "Benutzername"
        },
        "LOGIN.PASSWORD": {
            "en": "Password",
            "de": "Passwort"
        },
        "LOGIN.LANGUAGE": {
            "en": "Language",
            "de": "Sprache"
        },
        "LOGIN.SELECT_LANGUAGE_VALUE": {
            "en": "use Preset",
            "de": "nutze Voreinstellung"
        },
        "LOGIN.LOGINBTN": {
            "en": "Login",
            "de": "Login"
        },
        "LOGIN.RESETBTN": {
            "en": "Reset",
            "de": "Zurücksetzen"
        },
        "LOGIN.PASSPHRASE_TITLE": {
            "en": "unlock Key",
            "de": "Key entsperren"
        },
        "LOGIN.PASSPHRASE_MSG": {
            "en": "Passphrase",
            "de": "Passphrase"
        },
        "LOGIN.PASSPHRASE_OK_BUTTON": {
            "en": "unlock",
            "de": "entsperren"
        },
        "LOGIN.GENERATE_RSA_KEY_TITLE": {
            "en": "setup secured connection",
            "de": "richte sichere Verbindung ein"
        },
        "LOGIN.GENERATE_RSA_KEY_MSG": {
            "en": "generating RSA KeyPair...please wait",
            "de": "generiere RSA Schlüssel...bitte warten"
        },
        "LOGIN.GENERATE_RSA_KEY_ERROR_TITLE": {
            "en": "Error",
            "de": "Fehler"
        },
        "LOGIN.GENERATE_RSA_KEY_ERROR_MSG": {
            "en": "Error while transferring Session-Key, please reload",
            "de": "Fehler in der Sessionübertragung, bitte WebApp neu laden"
        },
        "LOGIN.INVALID_PASSPHRASE_MSG": {
            "en": "Invalid Passphrase",
            "de": "ungültige Passphrase"
        },


        "ADMIN.HEADLINE": {
            "en": "Administration",
            "de": "Administration"
        },


        "ELEMENTGRID.TITLE": {
            "en": "Title",
            "de": "Titel"
        },
        "ELEMENTGRID.URL": {
            "en": "Url",
            "de": "Url"
        },
        "ELEMENTGRID.USERNAME": {
            "en": "Username",
            "de": "Benutzername"
        },
        "ELEMENTGRID.PASSWORD": {
            "en": "Password",
            "de": "Passwort"
        },
        "ELEMENTGRID.COMMENT": {
            "en": "Comment",
            "de": "Kommentar"
        },

        "ELEMENT.ELEMENT_NOT_ENCRYPTED_ERROR_TITLE": {
            "en": "Error",
            "de": "Fehler"
        },
        "ELEMENT.ELEMENT_NOT_ENCRYPTED_ERROR_MSG": {
            "en": "Element is not encrypted for you!",
            "de": "Element ist nicht für dich verschlüsselt!"
        },
        "ELEMENT.NEW_ENTRY_TEXT": {
            "en": "New Entry",
            "de": "Neuer Eintrag"
        },
        "ELEMENT.COPY_ENTRY_TEXT": {
            "en": "Copy",
            "de": "Kopieren"
        },
        "ELEMENT.PASTE_ENTRY_TEXT": {
            "en": "Paste",
            "de": "Einfügen"
        },
        "ELEMENT.DELETE_ENTRY_TEXT": {
            "en": "Delete",
            "de": "Löschen"
        },
        "ELEMENT.DELETE_ENTRY_CONFIRMATION_TITLE": {
            "en": "are you sure?",
            "de": "bist du sicher?"
        },
        "ELEMENT.DELETE_ENTRY_CONFIRMATION_TEXT": {
            "en": "do you really want to delete this entry?",
            "de": "möchtest du den Eintrag wirklich löschen?"
        },

        "MANAGEMENT.SET_LANGUAGE_ERROR_TITLE": {
            "en": "Error",
            "de": "Fehler"
        },
        "MANAGEMENT.SET_LANGUAGE_ERROR_TEXT": {
            "en": "Error while setting new language",
            "de": "Fehler beim setzen der neuen Sprache"
        },
        "MANAGEMENT.SELECT_GROUP_MENU_LABEL": {
            "en": "select Group",
            "de": "wähle Gruppe"
        },
        "MANAGEMENT.CLEAR_LOCAL_CACHE_RELOAD_CONFIRMATION_TITLE": {
            "en": "reload application",
            "de": "Anwendung neu laden"
        },
        "MANAGEMENT.CHANGE_LANGUAGE_RELOAD_CONFIRMATION_TITLE": {
            "en": "reload application",
            "de": "Anwendung neu laden"
        },
        "MANAGEMENT.CHANGE_LANGUAGE_RELOAD_CONFIRMATION_TEXT": {
            "en": "the application needs to be reloaded",
            "de": "Die Anwendung muss neu geladen werden"
        },
        "MANAGEMENT.CLEAR_LOCAL_CACHE_RELOAD_CONFIRMATION_TEXT": {
            "en": "the application needs to be reloaded",
            "de": "Die Anwendung muss neu geladen werden"
        },

        "HEADER.SETTINGSBTN": {
            "en": "Settings",
            "de": "Einstellungen"
        },
        "HEADER.LOGOUTBTN": {
            "en": "Logout",
            "de": "Ausloggen"
        },
        "HEADER.LOGGED_IN_TEXT": {
            "en": "Logged in",
            "de": "Eingeloggt als"
        },
        "HEADER.ENCRYPTION_BUTTON": {
            "en": "encryption",
            "de": "Verschlüsselungen"
        },


        "NOTIFICATIONS.SUCCESS_NOTIFICATION_TITLE": {
            "en": "Success",
            "de": "Erfolg"
        },
        "NOTIFICATIONS.ERROR_NOTIFICATION_TITLE": {
            "en": "Error",
            "de": "Fehler"
        },


        "ENCRYPTION.START_TASK_TOOLTIP": {
            "en": "start encryption task for user",
            "de": "starte Verschlüsselungs-Task für diesen Benutzer"
        },
        "ENCRYPTION.DELETE_TASK_TOOLTIP": {
            "en": "delete encryption task",
            "de": "löschen diesen Verschlüsselungs-Task"
        },
        "ENCRYPTION.WINDOW_TITLE": {
            "en": "Encryption",
            "de": "Verschlüsselung"
        },
        "ENCRYPTION.ENCRYPT_ALL_BTN": {
            "en": "encrypt all",
            "de": "alle verschlüsseln"
        },


        "ADMIN.DIRECTORY_GRID_NAME_TEXT": {
            "en": "Name",
            "de": "Name"
        },
        "ADMIN.DIRECTORY_GRID_TYPE_TEXT": {
            "en": "Type",
            "de": "Typ"
        },
        "ADMIN.DIRECTORY_GRID_ADAPTER_TEXT": {
            "en": "Adapter",
            "de": "Adapter"
        },
        "ADMIN.DIRECTORY_GRID_SORTING_TEXT": {
            "en": "Sort",
            "de": "Sortierung"
        },
        "ADMIN.DIRECTORY_GRID_ACTIONS_TEXT": {
            "en": "Actions",
            "de": "Aktionen"
        },
        "ADMIN.DIRECTORY_GRID_UP_TOOLTIP": {
            "en": "Up",
            "de": "nach oben"
        },
        "ADMIN.DIRECTORY_GRID_DOWN_TOOLTIP": {
            "en": "Down",
            "de": "nach unten"
        },
        "ADMIN.DIRECTORY_GRID_EDIT_TOOLTIP": {
            "en": "edit",
            "de": "editieren"
        },
        "ADMIN.DIRECTORY_GRID_SYNC_TOOLTIP": {
            "en": "sync backend",
            "de": "synchronisiere Backend"
        },
        "ADMIN.DIRECTORY_GRID_DELETE_TOOLTIP": {
            "en": "delete",
            "de": "löschen"
        },
        "ADMIN.DIRECTORY_DELETE_ENTRY_CONFIRMATION_TITLE": {
            "en": "are you sure?",
            "de": "bist du sicher?"
        },
        "ADMIN.DIRECTORY_DELETE_ENTRY_CONFIRMATION_TEXT": {
            "en": "do you really want to delete this directory?",
            "de": "Verzeichnis wirklich löschen?"
        },

        "ADMIN.DIRECTORY_NEW_BTN": {
            "en": "New Directory",
            "de": "Neues Verzeichnis"
        },
        "ADMIN.DIRECTORY_WIZARD_SELECT_TYPE_DROPDOWN_LABEL": {
            "en": "Select type",
            "de": "wähle Typ"
        },

        "ADMIN.DIRECTORY_WIZARD_SELECT_TYPE_BTN": {
            "en": "select",
            "de": "auswählen"
        },


        "ADMIN.DIRECTORY_IMPLEMENTATION_FORM_LDAP_TYPE_LABEL": {
            "en": "Directory type",
            "de": "Verzeichnistyp"
        },
        "ADMIN.DIRECTORY_IMPLEMENTATION_FORM_LDAP_NAME_LABEL": {
            "en": "Name",
            "de": "Name"
        },
        "ADMIN.DIRECTORY_IMPLEMENTATION_FORM_LDAP_HOSTNAME_LABEL": {
            "en": "Hostname",
            "de": "Hostname"
        },
        "ADMIN.DIRECTORY_IMPLEMENTATION_FORM_LDAP_SECURITY_LABEL": {
            "en": "Security",
            "de": "Sicherheit"
        },
        "ADMIN.DIRECTORY_IMPLEMENTATION_FORM_LDAP_PORT_LABEL": {
            "en": "Port",
            "de": "Port"
        },
        "ADMIN.DIRECTORY_IMPLEMENTATION_FORM_LDAP_ANONYMOUS_BIND_LABEL": {
            "en": "anonymous bind",
            "de": "anonymous bind"
        },
        "ADMIN.DIRECTORY_IMPLEMENTATION_FORM_LDAP_USERNAME_LABEL": {
            "en": "Username",
            "de": "Benutzername"
        },
        "ADMIN.DIRECTORY_IMPLEMENTATION_FORM_LDAP_PASSWORD_LABEL": {
            "en": "Password",
            "de": "Passwort"
        },
        "ADMIN.DIRECTORY_IMPLEMENTATION_FORM_LDAP_BASEDN_LABEL": {
            "en": "base dn",
            "de": "Basis-DN"
        },
        "ADMIN.DIRECTORY_IMPLEMENTATION_FORM_LDAP_SYNCINTERVAL_LABEL": {
            "en": "sync interval",
            "de": "sync interval"
        },
        "ADMIN.DIRECTORY_IMPLEMENTATION_FORM_LDAP_READ_TIMEOUT_LABEL": {
            "en": "read timeout",
            "de": "Zeitüberschreitung"
        },
        "ADMIN.DIRECTORY_IMPLEMENTATION_FORM_LDAP_OBJECTCLASS_LABEL": {
            "en": "objectclass",
            "de": "ObjectClass"
        },
        "ADMIN.DIRECTORY_IMPLEMENTATION_FORM_LDAP_FILTER_LABEL": {
            "en": "filter",
            "de": "Filter"
        },
        "ADMIN.DIRECTORY_IMPLEMENTATION_FORM_LDAP_USER_ATTR_LABEL": {
            "en": "user attribute",
            "de": "Benutzer Attribut"
        },
        "ADMIN.DIRECTORY_IMPLEMENTATION_FORM_LDAP_DISPLAYNAME_ATTR_LABEL": {
            "en": "displayname attribute",
            "de": "Anzeigename Attribut"
        },
        "ADMIN.DIRECTORY_IMPLEMENTATION_FORM_LDAP_EMAIL_ATTR_LABEL": {
            "en": "email attribute",
            "de": "Email Attribut"
        },
        "ADMIN.DIRECTORY_IMPLEMENTATION_FORM_LDAP_SUBMIT_BTN_TEXT": {
            "en": "Submit",
            "de": "Absenden"
        },


        "ADMIN.DIRECTORY_IMPLEMENTATION_PREVIEW_GRID_FULLNAME_TEXT": {
            "en": "FullName",
            "de": "vollständiger Name"
        },
        "ADMIN.DIRECTORY_IMPLEMENTATION_PREVIEW_GRID_USERNAME_TEXT": {
            "en": "Username",
            "de": "Benutzername"
        },
        "ADMIN.DIRECTORY_IMPLEMENTATION_PREVIEW_GRID_EMAIL_TEXT": {
            "en": "email address",
            "de": "E-Mail Adresse"
        },


        "ADMIN.DIRECTORY_IMPLEMENTATION_LDAP_PANEL_TITLE": {
            "en": "Connection settings",
            "de": "Verbindungseinstellungen"
        },

        "ADMIN.DIRECTORY_IMPLEMENTATION_PREVIEW_STATUS_TEXTAREA_LABEL": {
            "en": "state",
            "de": "Status"
        },
        "ADMIN.DIRECTORY_IMPLEMENTATION_PREVIEW_STATUS_CONN_TEST_BTN_TEXT": {
            "en": "test connection",
            "de": "Verbindung testen"
        },


        "ADMIN.GROUP_AVAILABLE_USERS_TEXT": {
            "en": "available Users",
            "de": "verfügbare Benutzer"
        },
        "ADMIN.GROUP_CURRENT_USERS_TEXT": {
            "en": "current Users",
            "de": "aktuelle Benutzer"
        },

        "ADMIN.GROUP_DETAIL_NAME_TEXT": {
            "en": "Group Name",
            "de": "Gruppen Name"
        },
        "ADMIN.GROUP_DETAIL_ISADMIN_TEXT": {
            "en": "Admin",
            "de": "Admin"
        },

        "ADMIN.GROUP_GRID_NAME_TEXT": {
            "en": "Group name",
            "de": "Gruppen Name"
        },
        "ADMIN.GROUP_GRID_ISADMIN_TEXT": {
            "en": "Administrator",
            "de": "Administrator"
        },
        "ADMIN.GROUP_GRID_ISADMIN_TRUE_TOOLTIP": {
            "en": "yes",
            "de": "ja"
        },
        "ADMIN.GROUP_GRID_ISADMIN_FALSE_TOOLTIP": {
            "en": "no",
            "de": "nein"
        },

        "ADMIN.GROUP_GRID_DELETE_TOOLTIP": {
            "en": "Delete",
            "de": "Löschen"
        },
        "ADMIN.GROUP_GRID_NEW_BTN": {
            "en": "New Group",
            "de": "Neue Gruppe"
        },
        "ADMIN.GROUP_GRID_SAVE_BTN": {
            "en": "Save",
            "de": "Speichern"
        },
        "ADMIN.GROUP_GRID_DELETE_CONFIRMATION_TITLE": {
            "en": "delete group",
            "de": "Gruppe löschen"
        },
        "ADMIN.GROUP_GRID_DELETE_CONFIRMATION_TEXT": {
            "en": "you really want do delete group '{0}'?",
            "de": "Wollen Sie wirklich Gruppe '{0}' löschen?"
        },


        "ADMIN.PERMISSION_GRID_TEXT": {
            "en": "Group",
            "de": "Gruppe"
        },
        "ADMIN.PERMISSION_GRID_READ": {
            "en": "Read",
            "de": "Lesen"
        },
        "ADMIN.PERMISSION_GRID_CREATE": {
            "en": "Create",
            "de": "Erstellen"
        },
        "ADMIN.PERMISSION_GRID_UPDATE": {
            "en": "Update",
            "de": "Aktualisieren"
        },
        "ADMIN.PERMISSION_GRID_DELETE": {
            "en": "Delete",
            "de": "Löschen"
        },
        "ADMIN.PERMISSION_GRID_INHERITED": {
            "en": "Inherited",
            "de": "Vererbt"
        },
        "ADMIN.PERMISSION_GRID_INHERITED_TOOLTIP": {
            "en": "inherited",
            "de": "vererbt"
        },
        "ADMIN.PERMISSION_GRID_DELETE_TOOLTIP": {
            "en": "Delete",
            "de": "Löschen"
        },
        "ADMIN.PERMISSION_GRID_ACTION_TEXT": {
            "en": "Actions",
            "de": "Aktionen"
        },
        "ADMIN.PERMISSION_AVAILABLE_GROUPS_TEXT": {
            "en": "available Groups",
            "de": "verfügbare Gruppen"
        },

        "ADMIN.PERMISSION_TREE_RESET_BTN": {
            "en": "Reset",
            "de": "Zurücksetzen"
        },
        "ADMIN.PERMISSION_TREE_SAVE_BTN": {
            "en": "Save",
            "de": "Speichern"
        },
        "ADMIN.PERMISSION_DELETE_ENTRY_CONFIRMATION_TITLE": {
            "en": "are you sure?",
            "de": "bist du sicher?"
        },
        "ADMIN.PERMISSION_DELETE_ENTRY_CONFIRMATION_TEXT": {
            "en": "do you really want to delete this permission?",
            "de": "Berechtigung wirklich löschen?"
        },

        "ADMIN.USER_AVAILABLE_GROUPS_TEXT": {
            "en": "available Groups",
            "de": "verfügbare Gruppen"
        },
        "ADMIN.USER_CURRENT_GROUPS_TEXT": {
            "en": "current Groups",
            "de": "aktuelle Gruppen"
        },

        "ADMIN.USER_DETAIL_FORM_USERNAME": {
            "en": "Username",
            "de": "Benutzername"
        },
        "ADMIN.USER_DETAIL_FORM_FULLNAME": {
            "en": "Full Name",
            "de": "vollständiger Name"
        },
        "ADMIN.USER_DETAIL_FORM_PASSWORD": {
            "en": "Password",
            "de": "Passwort"
        },
        "ADMIN.USER_DETAIL_FORM_EMAIL": {
            "en": "Email",
            "de": "Email"
        },
        "ADMIN.USER_DETAIL_FORM_ENABLED": {
            "en": "Enabled",
            "de": "Aktiviert"
        },


        "ADMIN.USER_GRID_USERNAME_TEXT": {
            "en": "Username",
            "de": "Benutzername"
        },
        "ADMIN.USER_GRID_FULLNAME_TEXT": {
            "en": "FullName",
            "de": "vollständiger Name"
        },
        "ADMIN.USER_GRID_EMAIL_TEXT": {
            "en": "email-address",
            "de": "Email-Adresse"
        },
        "ADMIN.USER_GRID_GROUPS_TEXT": {
            "en": "Groups",
            "de": "Gruppen"
        },
        "ADMIN.USER_GRID_DIRECTORY_TEXT": {
            "en": "Directoy-Server",
            "de": "Verzeichnis-Server"
        },
        "ADMIN.USER_GRID_STATUS_TEXT": {
            "en": "State",
            "de": "Status"
        },
        "ADMIN.USER_GRID_SETUP_COMPLETED_TEXT": {
            "en": "Setup completed",
            "de": "Setup vollständig"
        },
        "ADMIN.USER_GRID_ACTIONS_TEXT": {
            "en": "Actions",
            "de": "Aktionen"
        },
        "ADMIN.USER_GRID_DELETE_TOOLTIP": {
            "en": "Delete",
            "de": "Löschen"
        },
        "ADMIN.USER_GRID_DELETE_CONFIRMATION_TITLE": {
            "en": "delete user",
            "de": "Benutzer löschen"
        },
        "ADMIN.USER_GRID_DELETE_CONFIRMATION_TEXT": {
            "en": "you really want to delete user '{0}'?",
            "de": "Wollen Sie wirklich User '{0}' löschen?"
        },
        "ADMIN.USER_GRID_ADD_TO_WORK_QUEUE_TOOLTIP": {
            "en": "add to encryption queue",
            "de": "zum verschlüsseln vormerken"
        },
        "ADMIN.ADD_USER_TO_WORK_QUEUE_MSG_TITLE": {
            "en": "Error",
            "de": "Fehler"
        },
        "ADMIN.ADD_USER_TO_WORK_QUEUE_MSG_TEXT": {
            "en": "error while adding user to queue",
            "de": "Fehler beim Hinzufügen des Users zur Warteschlange"
        },
        "ADMIN.USER_GRID_ENABLED_TRUE_TOOLTIP": {
            "en": "enabled",
            "de": "aktiviert"
        },
        "ADMIN.USER_GRID_ENABLED_FALSE_TOOLTIP": {
            "en": "disabled",
            "de": "deaktiviert"
        },
        "ADMIN.USER_GRID_SETUP_COMPLETED_TRUE_TOOLTIP": {
            "en": "Setup complete",
            "de": "Einrichtung abgeschlossen"
        },
        "ADMIN.USER_GRID_SETUP_COMPLETED_FALSE_TOOLTIP": {
            "en": "Setup incomplete",
            "de": "Einrichtung nicht abgeschlossen"
        },



        "ADMIN.USER_GRID_NEW_BTN": {
            "en": "New User",
            "de": "Neuer Benutzer"
        },
        "ADMIN.USER_GRID_SAVE_BTN": {
            "en": "Save",
            "de": "Speichern"
        },
        "ADMIN.SETTINGS_GRID_KEY_TEXT": {
            "en": "Setting-Name",
            "de": "Einstellungsname"
        },
        "ADMIN.SETTINGS_GRID_DEFAULT_VALUE_TEXT": {
            "en": "Default Setting",
            "de": "Standardeinstellung"
        },
        "ADMIN.SETTINGS_GRID_CUSTOM_VALUE_TEXT": {
            "en": "Custom Setting",
            "de": "Benutzerdefinierte Einstellung"
        },
        "ADMIN.SETTINGS_GRID_SAVE_BTN": {
            "en": "Save",
            "de": "Speichern"
        },


        "SETTINGS.WINDOW_TITLE": {
            "en": "Settings",
            "de": "Einstellungen"
        },
        "SETTINGS.CHANGE_PASSWORD_FORM_TITLE": {
            "en": "Change Password",
            "de": "Passwort ändern"
        },
        "SETTINGS.GENERATE_NEW_RSA_KEY_FORM_TITLE": {
            "en": "Change RSA passphrase",
            "de": "RSA Passphrase ändern"
        },
        "SETTINGS.MISC_PANEL_TITLE": {
            "en": "Miscellaneous",
            "de": "Verschiedenes"
        },
        "SETTINGS.CURRENT_PASSWORD_TEXT": {
            "en": "current Password",
            "de": "aktuelles Passwort"
        },
        "SETTINGS.NEW_PASSWORD_TEXT": {
            "en": "new Password",
            "de": "neues Passwort"
        },
        "SETTINGS.REPEAT_NEW_PASSWORD_TEXT": {
            "en": "repeat new Password",
            "de": "Password wiederholen"
        },
        "SETTINGS.SET_PASSWORD_BTN": {
            "en": "Submit",
            "de": "Speichern"
        },
        "SETTINGS.CURRENT_RSA_PASSPHRASE_TEXT": {
            "en": "current RSA Passphrase",
            "de": "aktuelle RSA Passphrase"
        },
        "SETTINGS.NEW_RSA_PASS_PHRASE_TEXT": {
            "en": "new RSA Passphrase",
            "de": "neue RSA Passphrase"
        },
        "SETTINGS.REPEAT_NEW_RSA_PASS_PHRASE_TEXT": {
            "en": "repeat new RSA Passphrase",
            "de": "RSA Passphrase wiederholen"
        },
        "SETTINGS.SUBMIT_NEW_RSA_PASS_PHRASE_TEXT": {
            "en": "Submit new RSA-Key",
            "de": "Neuen RSA-Key übertragen"
        },
        "SETTINGS.NEW_RSA_PASSPHRASE_CURRENT_PASSPHRASE_MISMATCH_ERROR": {
            "en": "current passphrase is not correct",
            "de": "aktuelle passphrase nicht korrekt"
        },
        "SETTINGS.NEW_RSA_PASSPHRASE_MISMATCH_ERROR": {
            "en": "Passphrases do not match",
            "de": "Passphrases stimmen nicht überein"
        },
        "SETTINGS.NEW_RSA_PASSPHRASE_COMPLEXITY_ERROR": {
            "en": "new passphrase doesn't complies with requirements",
            "de": "Neue Passphrase entspricht nicht den Anforderungen"
        },
        "SETTINGS.CHANGE_LANGUAGE_FORM_TITLE": {
            "en": "Language",
            "de": "Sprache"
        },
        "SETTINGS.SET_LANGUAGE_TEXT": {
            "en": "use Language",
            "de": "Sprache"
        },
        "SETTINGS.SET_LANGUAGE_BTN": {
            "en": "Save",
            "de": "Speichern"
        },
        "SETTINGS.SUBMIT_CLEAR_LOCAL_CACHE": {
            "en": "clear local cache",
            "de": "Cache leeren"
        },
        "SETTINGS.USE_PINKTHEME_FIELDLABEL": {
            "en": "use PinkTheme",
            "de": "nutze PinkTheme"
        },
        "SETTINGS.TREE_SORT_ORDER_FIELDLABEL": {
            "en": "alphabetical order?",
            "de": "alphabetische Sortierung?"

        },
        "SETTINGS.CHANGE_THEME_RELOAD_TITLE": {
            "en": "reload application?",
            "de": "Applikation neu laden?"
        },
        "SETTINGS.CHANGE_THEME_RELOAD_TEXT": {
            "en": "To make the changes effective you have reload the application.<br /> Do you want to reload now?",
            "de": "Damit die Änderungen wirksam werden, muss die Application neu geladen werden. Jetzt neu laden?"
        },
        "SETTINGS.CHANGE_TREE_SORT_ORDER_RELOAD_TITLE": {
            "en": "reload application?",
            "de": "Applikation neu laden?"
        },
        "SETTINGS.CHANGE_TREE_SORT_ORDER_RELOAD_TEXT": {
            "en": "To make the changes effective you have reload the application.<br /> Do you want to reload now?",
            "de": "Damit die Änderungen wirksam werden, muss die Application neu geladen werden. Jetzt neu laden?"
        },
        "SETUP.PASSWORD": {
            "en": "Password",
            "de": "Passwort"
        },
        "SETUP.REPEAT_PASSWORD": {
            "en": "Confirm Password",
            "de": "Passwort bestätigen"
        },
        "SETUP.PROCEED": {
            "en": "proceed",
            "de": "fortfahren"
        },
        "SETUP.PASSWORD_MISMATCH": {
            "en": "Passwords do not match",
            "de": "Passwörter stimmen nicht überein"
        },
        "SETUP.SETUP_WINDOW_TITLE": {
            "en": "set a password",
            "de": "bestimme ein Password"
        },
        "SETUP.ESTABLISH_ACCOUNT_TITLE": {
            "en": "Setup-Wizard",
            "de": "Setup-Assistent"
        },
        "SETUP.ESTABLISH_ACCOUNT_MSG": {
            "en": "Account is getting established...",
            "de": "Account wird vollständig eingerichtet..."
        },
        "SETUP.SETUP_RSA_TITLE": {
            "en": "Success",
            "de": "Erfolg"
        },
        "SETUP.SETUP_RSA_MSG": {
            "en": "Setup finished",
            "de": "Einrichtung abgeschlossen"
        },
        "SETUP.SETUP_RSA_BUTTON": {
            "en": "close",
            "de": "schließen"
        },

        "ELEMENT.DEFAULT_FORM_TITLE": {
            "en": "Title",
            "de": "Titel"
        },
        "ELEMENT.DEFAULT_FORM_URL": {
            "en": "Url",
            "de": "Url"
        },
        "ELEMENT.DEFAULT_FORM_USERNAME": {
            "en": "Username",
            "de": "Benutzername"
        },
        "ELEMENT.DEFAULT_FORM_PASSWORD": {
            "en": "Password",
            "de": "Passwort"
        },
        "ELEMENT.DEFAULT_FORM_UNHIDE_BTN": {
            "en": "unhide",
            "de": "einblenden"
        },
        "ELEMENT.DEFAULT_FORM_HIDE_BTN": {
            "en": "hide",
            "de": "ausblenden"
        },
        "ELEMENT.DEFAULT_FORM_COMMENT": {
            "en": "Comment",
            "de": "Kommentar"
        },
        "ELEMENT.DEFAULT_FORM_SAVE_BTN": {
            "en": "Save",
            "de": "Speichern"
        },
        "ELEMENT.DEFAULT_FORM_COPY_BTN": {
            "en": "Copy",
            "de": "Kopieren"
        },
        "ELEMENT.DEFAULT_FORM_OPEN_URL_BTN": {
            "en": "Open",
            "de": "Öffnen"
        },


        "ELEMENT.RTE_FORM_TITLE": {
            "en": "Title",
            "de": "Titel"
        },
        "ELEMENT.RTE_FORM_SUBMIT_BTN": {
            "en": "Save",
            "de": "Speichern"
        },
        "ELEMENT.CHOOSE_TEMPLATE_TEXT": {
            "en": "Choose Template",
            "de": "Template wählen"
        }


    }
});
