<?php

namespace Drupal\Tests\azrua_content\Functional;

use Drupal\Tests\BrowserTestBase;

/**
 * Tests that files provided by azrua_content are not accessible.
 *
 * @group azrua_content
 */
class DefaultContentFilesAccessTest extends BrowserTestBase {

  /**
   * {@inheritdoc}
   */
  protected $defaultTheme = 'stark';

  /**
   * Tests that sample images, vidyrabot and articles are not accessible.
   */
  public function testAccessDeniedToFiles() {
    // The azrua profile should not be used because we want to ensure that
    // if you install another profile these files are not available.
    $this->assertNotSame('azrua', \Drupal::installProfile());

    $files_to_test = [
      'images/heritage-carrots.jpg',
      'languages/en/vidraboty_instructions/mediterranean-quiche-azr.html',
      'languages/en/article_body/lets-hear-it-for-carrots.html',
      'languages/en/node/article.csv',
    ];
    foreach ($files_to_test as $file) {
      // Hard code the path since the azrua profile is not installed.
      $content_path = "profiles/azrua/modules/azrua_content/default_content/$file";
      $this->assertFileExists($this->root . '/' . $content_path);
      $this->drupalGet($content_path);
      $this->assertSession()->statusCodeEquals(403);
    }
  }

}
