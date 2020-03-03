<?php
namespace Neos\Flow\Persistence\Doctrine\Migrations;

use Doctrine\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;
use Doctrine\DBAL\Migrations\AbortMigrationException;

/**
 * Auto-generated Migration: Please modify to your needs! This block will be used as the migration description if getDescription() is not used.
 */
class Version20200223175749 extends AbstractMigration
{

    /**
     * @return string
     */
    public function getDescription(): string
    {
        return '';
    }

    /**
     * @param Schema $schema
     * @return void
     * @throws AbortMigrationException
     */
    public function up(Schema $schema): void
    {
        // this up() migration is autogenerated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on "mysql".');

        $this->addSql('CREATE TABLE teampass_core_domain_model_acl (id INT AUTO_INCREMENT NOT NULL, usergroup INT DEFAULT NULL, grouptreeelement INT DEFAULT NULL, pread TINYINT(1) NOT NULL, pcreate TINYINT(1) NOT NULL, pupdate TINYINT(1) NOT NULL, pdelete TINYINT(1) NOT NULL, inherited TINYINT(1) NOT NULL, INDEX IDX_31F12E574A647817 (usergroup), INDEX IDX_31F12E573195BF81 (grouptreeelement), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE teampass_core_domain_model_directory (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, configuration LONGTEXT DEFAULT NULL, positionindex INT DEFAULT NULL, type VARCHAR(255) DEFAULT NULL, implementationclass VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE teampass_core_domain_model_elementtemplate (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, internalname VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE teampass_core_domain_model_encryptedcontent (id INT AUTO_INCREMENT NOT NULL, groupelement INT DEFAULT NULL, template INT DEFAULT NULL, timestamp DATETIME DEFAULT NULL, content LONGTEXT NOT NULL, INDEX IDX_786D245C6BF5616A (groupelement), INDEX IDX_786D245C97601F83 (template), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE teampass_core_domain_model_groupelement (id INT AUTO_INCREMENT NOT NULL, grouptreeelement INT DEFAULT NULL, name VARCHAR(255) NOT NULL, positionindex INT DEFAULT NULL, tags VARCHAR(255) NOT NULL, description VARCHAR(255) NOT NULL, INDEX IDX_B0D33EB53195BF81 (grouptreeelement), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE teampass_core_domain_model_grouptreeelement (id INT AUTO_INCREMENT NOT NULL, parent INT DEFAULT NULL, name VARCHAR(255) NOT NULL, `index` INT NOT NULL, expanded TINYINT(1) NOT NULL, leaf TINYINT(1) NOT NULL, isroot TINYINT(1) DEFAULT NULL, UNIQUE INDEX UNIQ_FA9DF7BB17FB4369 (isroot), INDEX IDX_FA9DF7BB3D8E604F (parent), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE teampass_core_domain_model_intermediatekey (id INT AUTO_INCREMENT NOT NULL, user INT DEFAULT NULL, groupelement INT DEFAULT NULL, encryptedaeskey LONGTEXT NOT NULL, rsaencryptedaeskey LONGTEXT NOT NULL, INDEX IDX_6F61D86C8D93D649 (user), INDEX IDX_6F61D86C6BF5616A (groupelement), UNIQUE INDEX UNIQ_6F61D86C8D93D6496BF5616A (user, groupelement), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE teampass_core_domain_model_setting (id INT AUTO_INCREMENT NOT NULL, settingname VARCHAR(191) NOT NULL, defaultvalue VARCHAR(255) NOT NULL, customvalue VARCHAR(255) DEFAULT NULL, UNIQUE INDEX UNIQ_E44F9424C6792AC2 (settingname), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE teampass_core_domain_model_usergroup (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, admin TINYINT(1) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE teampass_core_domain_model_workqueue (id INT AUTO_INCREMENT NOT NULL, user INT DEFAULT NULL, action VARCHAR(255) DEFAULT NULL, UNIQUE INDEX UNIQ_A1B3482C8D93D649 (user), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE teampass_core_domain_model_user (id INT AUTO_INCREMENT NOT NULL, directory INT DEFAULT NULL, email VARCHAR(255) DEFAULT NULL, username VARCHAR(255) NOT NULL, password VARCHAR(255) DEFAULT NULL, language VARCHAR(255) DEFAULT NULL, theme VARCHAR(255) DEFAULT NULL, alphabeticalorder TINYINT(1) DEFAULT NULL, fullname VARCHAR(255) DEFAULT NULL, privatekey LONGTEXT DEFAULT NULL, rsasalt VARCHAR(255) DEFAULT NULL, publickey LONGTEXT DEFAULT NULL, enabled TINYINT(1) NOT NULL, treesettings LONGTEXT DEFAULT NULL, deleted TINYINT(1) DEFAULT NULL, INDEX IDX_8BA81215467844DA (directory), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE teampass_core_domain_model_user_groups_join (core_user INT NOT NULL, core_usergroup INT NOT NULL, INDEX IDX_3715C749BF76157C (core_user), INDEX IDX_3715C7495044833 (core_usergroup), PRIMARY KEY(core_user, core_usergroup)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE teampass_core_domain_model_acl ADD CONSTRAINT FK_31F12E574A647817 FOREIGN KEY (usergroup) REFERENCES teampass_core_domain_model_usergroup (id)');
        $this->addSql('ALTER TABLE teampass_core_domain_model_acl ADD CONSTRAINT FK_31F12E573195BF81 FOREIGN KEY (grouptreeelement) REFERENCES teampass_core_domain_model_grouptreeelement (id)');
        $this->addSql('ALTER TABLE teampass_core_domain_model_encryptedcontent ADD CONSTRAINT FK_786D245C6BF5616A FOREIGN KEY (groupelement) REFERENCES teampass_core_domain_model_groupelement (id)');
        $this->addSql('ALTER TABLE teampass_core_domain_model_encryptedcontent ADD CONSTRAINT FK_786D245C97601F83 FOREIGN KEY (template) REFERENCES teampass_core_domain_model_elementtemplate (id)');
        $this->addSql('ALTER TABLE teampass_core_domain_model_groupelement ADD CONSTRAINT FK_B0D33EB53195BF81 FOREIGN KEY (grouptreeelement) REFERENCES teampass_core_domain_model_grouptreeelement (id)');
        $this->addSql('ALTER TABLE teampass_core_domain_model_grouptreeelement ADD CONSTRAINT FK_FA9DF7BB3D8E604F FOREIGN KEY (parent) REFERENCES teampass_core_domain_model_grouptreeelement (id)');
        $this->addSql('ALTER TABLE teampass_core_domain_model_intermediatekey ADD CONSTRAINT FK_6F61D86C8D93D649 FOREIGN KEY (user) REFERENCES teampass_core_domain_model_user (id)');
        $this->addSql('ALTER TABLE teampass_core_domain_model_intermediatekey ADD CONSTRAINT FK_6F61D86C6BF5616A FOREIGN KEY (groupelement) REFERENCES teampass_core_domain_model_groupelement (id)');
        $this->addSql('ALTER TABLE teampass_core_domain_model_workqueue ADD CONSTRAINT FK_A1B3482C8D93D649 FOREIGN KEY (user) REFERENCES teampass_core_domain_model_user (id)');
        $this->addSql('ALTER TABLE teampass_core_domain_model_user ADD CONSTRAINT FK_8BA81215467844DA FOREIGN KEY (directory) REFERENCES teampass_core_domain_model_directory (id)');
        $this->addSql('ALTER TABLE teampass_core_domain_model_user_groups_join ADD CONSTRAINT FK_3715C749BF76157C FOREIGN KEY (core_user) REFERENCES teampass_core_domain_model_user (id)');
        $this->addSql('ALTER TABLE teampass_core_domain_model_user_groups_join ADD CONSTRAINT FK_3715C7495044833 FOREIGN KEY (core_usergroup) REFERENCES teampass_core_domain_model_usergroup (id)');
    }

    /**
     * @param Schema $schema
     * @return void
     * @throws AbortMigrationException
     */
    public function down(Schema $schema): void
    {
        // this down() migration is autogenerated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on "mysql".');

        $this->addSql('ALTER TABLE teampass_core_domain_model_user DROP FOREIGN KEY FK_8BA81215467844DA');
        $this->addSql('ALTER TABLE teampass_core_domain_model_encryptedcontent DROP FOREIGN KEY FK_786D245C97601F83');
        $this->addSql('ALTER TABLE teampass_core_domain_model_encryptedcontent DROP FOREIGN KEY FK_786D245C6BF5616A');
        $this->addSql('ALTER TABLE teampass_core_domain_model_intermediatekey DROP FOREIGN KEY FK_6F61D86C6BF5616A');
        $this->addSql('ALTER TABLE teampass_core_domain_model_acl DROP FOREIGN KEY FK_31F12E573195BF81');
        $this->addSql('ALTER TABLE teampass_core_domain_model_groupelement DROP FOREIGN KEY FK_B0D33EB53195BF81');
        $this->addSql('ALTER TABLE teampass_core_domain_model_grouptreeelement DROP FOREIGN KEY FK_FA9DF7BB3D8E604F');
        $this->addSql('ALTER TABLE teampass_core_domain_model_acl DROP FOREIGN KEY FK_31F12E574A647817');
        $this->addSql('ALTER TABLE teampass_core_domain_model_user_groups_join DROP FOREIGN KEY FK_3715C7495044833');
        $this->addSql('ALTER TABLE teampass_core_domain_model_intermediatekey DROP FOREIGN KEY FK_6F61D86C8D93D649');
        $this->addSql('ALTER TABLE teampass_core_domain_model_workqueue DROP FOREIGN KEY FK_A1B3482C8D93D649');
        $this->addSql('ALTER TABLE teampass_core_domain_model_user_groups_join DROP FOREIGN KEY FK_3715C749BF76157C');
        $this->addSql('DROP TABLE teampass_core_domain_model_acl');
        $this->addSql('DROP TABLE teampass_core_domain_model_directory');
        $this->addSql('DROP TABLE teampass_core_domain_model_elementtemplate');
        $this->addSql('DROP TABLE teampass_core_domain_model_encryptedcontent');
        $this->addSql('DROP TABLE teampass_core_domain_model_groupelement');
        $this->addSql('DROP TABLE teampass_core_domain_model_grouptreeelement');
        $this->addSql('DROP TABLE teampass_core_domain_model_intermediatekey');
        $this->addSql('DROP TABLE teampass_core_domain_model_setting');
        $this->addSql('DROP TABLE teampass_core_domain_model_usergroup');
        $this->addSql('DROP TABLE teampass_core_domain_model_workqueue');
        $this->addSql('DROP TABLE teampass_core_domain_model_user');
        $this->addSql('DROP TABLE teampass_core_domain_model_user_groups_join');
    }


    public function postUp(Schema $schema) : void
    {
        $this->connection->insert(
            'teampass_core_domain_model_directory',
            array(
                'name' => 'Internal',
                'configuration' => null,
                'positionindex' => 0,
                'type' => 'internal',
                'implementationclass' => 'NoAdapter'
            )
        );

        $this->connection->insert(
            'teampass_core_domain_model_elementtemplate',
            array(
                'name' => 'Default Template',
                'internalname' => 'DEFAULT_TEMPLATE'
            )
        );

        $this->connection->insert(
            'teampass_core_domain_model_elementtemplate',
            array(
                'name' => 'RTE Template',
                'internalname' => 'RTE'
            )
        );

        $userGroupId = $this->connection->insert(
            'teampass_core_domain_model_usergroup',
            array(
                'name' => 'Administrators',
                'admin' => true
            )
        );

        $groupTreeId = $this->connection->insert(
            'teampass_core_domain_model_grouptreeelement',
            array(
                'name' => 'Groups',
                '`index`' => 0,
                'expanded' => true,
                'leaf' => 0,
                'isroot' => true
            )
        );

        $this->connection->insert(
            'teampass_core_domain_model_acl',
            array(
                'usergroup' => $userGroupId,
                'pread' => true,
                'pcreate' => true,
                'pupdate' => true,
                'pdelete' => true,
                'inherited' => 0,
                'grouptreeelement' => $groupTreeId
            )
        );

        $this->connection->insert(
            'teampass_core_domain_model_setting',
            array(
                'settingname' => 'directory.internal.forcePasswordComplexity',
                'defaultvalue' => 'true'
            )
        );

        $this->connection->insert(
            'teampass_core_domain_model_setting',
            array(
                'settingname' => 'directory.internal.passwordRegularExpression',
                'defaultvalue' => '/(?=.{9,})(?=.*[a-z])(?=.*[A-Z])(?=.*[0-9]).*/'
            )
        );

        $this->connection->insert(
            'teampass_core_domain_model_setting',
            array(
                'settingname' => 'rsa.passPhrase.forcePassPhraseComplexity',
                'defaultvalue' => 'true'
            )
        );

        $this->connection->insert(
            'teampass_core_domain_model_setting',
            array(
                'settingname' => 'rsa.passPhrase.passwordRegularExpression',
                'defaultvalue' => '/(?=.{9,})(?=.*[a-z])(?=.*[A-Z])(?=.*[0-9]).*/'
            )
        );

        $this->connection->insert(
            'teampass_core_domain_model_setting',
            array(
                'settingname' => 'pollInterval',
                'defaultvalue' => '10'
            )
        );

        $this->connection->insert(
            'teampass_core_domain_model_setting',
            array(
                'settingname' => 'encryption.batchSize',
                'defaultvalue' => '100'
            )
        );
    }
}
