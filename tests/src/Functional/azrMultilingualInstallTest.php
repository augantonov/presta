<?php

namespace Drupal\Tests\azrua\Functional;

use Drupal\FunctionalTests\Installer\InstallerTestBase;

/**
 * Tests the multilingual installer installing the AZR profile.
 *
 * @group Installer
 */
class AZRMultilingualInstallTest extends InstallerTestBase {

  /**
   * {@inheritdoc}
   */
  protected $profile = 'azrua';

  /**
   * {@inheritdoc}
   */
  protected $langcode = 'uk';

  /**
   * Ensures that AZR can be installed with Spanish as the default language.
   */
  public function testAZR() {
    $this->drupalGet('');
    // cSpell:disable-next-line
    $this->assertSession()->pageTextContains('Quiche mediterráneo profundo');
  }

  /**
   * {@inheritdoc}
   */
  protected function setUpLanguage() {
    // Place custom local translations in the translations directory to avoid
    // getting translations from localize.drupal.org.
    mkdir(DRUPAL_ROOT . '/' . $this->siteDirectory . '/profiles/presta-master/translations', 0777, TRUE);
    file_put_contents(DRUPAL_ROOT . '/' . $this->siteDirectory . '/profiles/presta-master/translations/drupal-9.3.0.ru.po', $this->getPo('ru'));

    parent::setUpLanguage();
  }

  /**
   * Returns the string for the test .po file.
   *
   * @param string $langcode
   *   The language code.
   *
   * @return string
   *   Contents for the test .po file.
   */
  protected function getPo($langcode) {
    return <<<ENDPO
msgid ""
msgstr ""

msgid "Save and continue"
msgstr "Save and continue $langcode"

msgid "Anonymous"
msgstr "Anonymous $langcode"

msgid "Language"
msgstr "Language $langcode"

#: Testing site name configuration during the installer.
msgid "Drupal"
msgstr "Drupal"
ENDPO;
  }

}
