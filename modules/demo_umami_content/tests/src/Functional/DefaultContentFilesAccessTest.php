<?php

namespace Drupal\Tests\demo_azr_content\Functional;

use Drupal\Tests\BrowserTestBase;

/**
 * Tests that files provided by demo_azr_content are not accessible.
 *
 * @group demo_azr_content
 */
class DefaultContentFilesAccessTest extends BrowserTestBase {

  /**
   * {@inheritdoc}
   */
  protected $defaultTheme = 'stark';

  /**
   * Tests that sample images, vid_rabotys and articles are not accessible.
   */
  public function testAccessDeniedToFiles() {
    // The demo_azr profile should not be used because we want to ensure that
    // if you install another profile these files are not available.
    $this->assertNotSame('demo_azr', \Drupal::installProfile());

    $files_to_test = [
      'images/heritage-carrots.jpg',
      'languages/en/vid_raboty_instructions/mediterranean-quiche-azr.html',
      'languages/en/article_body/lets-hear-it-for-carrots.html',
      'languages/en/node/article.csv',
    ];
    foreach ($files_to_test as $file) {
      // Hard code the path since the demo_azr profile is not installed.
      $content_path = "core/profiles/demo_azr/modules/demo_azr_content/default_content/$file";
      $this->assertFileExists($this->root . '/' . $content_path);
      $this->drupalGet($content_path);
      $this->assertSession()->statusCodeEquals(403);
    }
  }

}
