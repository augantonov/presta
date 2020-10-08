<?php

namespace Drupal\Tests\azrua_content\Functional;

use Drupal\Core\Entity\EntityStorageInterface;
use Drupal\Tests\BrowserTestBase;

/**
 * Tests that uninstalling default content removes created content.
 *
 * @group azrua_content
 */
class UninstallDefaultContentTest extends BrowserTestBase {

  /**
   * {@inheritdoc}
   */
  protected $profile = 'azrua';

  /**
   * Tests uninstalling content removes created entities.
   */
  public function testReinstall() {
    $module_installer = $this->container->get('module_installer');

    // Test imported blocks on profile install.
    $block_storage = $this->container->get('entity_type.manager')->getStorage('block_content');
    $this->assertImportedCustomBlock($block_storage);

    // Test imported nodes on profile install.
    $node_storage = $this->container->get('entity_type.manager')->getStorage('node');
    $this->assertVidyrabotImported($node_storage);

    $count = $node_storage->getQuery()
      ->condition('type', 'article')
      ->count()
      ->execute();
    $this->assertGreaterThan(0, $count);

    // Uninstall the module.
    $module_installer->uninstall(['azrua_content']);

    // Reset storage cache.
    $block_storage->resetCache();
    $node_storage->resetCache();

    // Assert the removal of blocks on uninstall.
    foreach ($this->expectedBlocks() as $block_info) {
      $count = $block_storage->getQuery()
        ->condition('type', $block_info['type'])
        ->count()
        ->execute();
      $this->assertEquals(0, $count);
      $block = $block_storage->loadByProperties(['uuid' => $block_info['uuid']]);
      $this->assertCount(0, $block);
    }

    // Assert the removal of nodes on uninstall.
    $count = $node_storage->getQuery()
      ->condition('type', 'article')
      ->count()
      ->execute();
    $this->assertEquals(0, $count);

    $count = $node_storage->getQuery()
      ->condition('type', 'vidraboty')
      ->count()
      ->execute();
    $this->assertEquals(0, $count);

    // Re-install and assert imported content.
    $module_installer->install(['azrua_content']);
    $this->assertVidyrabotImported($node_storage);
    $this->assertArticlesImported($node_storage);
    $this->assertImportedCustomBlock($block_storage);
  }

  /**
   * Assert vidyrabot are imported.
   *
   * @param \Drupal\Core\Entity\EntityStorageInterface $node_storage
   *   Node storage.
   */
  protected function assertVidyrabotImported(EntityStorageInterface $node_storage) {
    $count = $node_storage->getQuery()
      ->condition('type', 'vidraboty')
      ->count()
      ->execute();
    $this->assertGreaterThan(0, $count);
    $nodes = $node_storage->loadByProperties(['title' => 'Gluten free pizza']);
    $this->assertCount(1, $nodes);
    $node = reset($nodes);
    $this->assertStringContainsString('Mix some of the milk and water in a jug', $node->field_vidraboty_instruction->value);
  }

  /**
   * Assert articles are imported.
   *
   * @param \Drupal\Core\Entity\EntityStorageInterface $node_storage
   *   Node storage.
   */
  protected function assertArticlesImported(EntityStorageInterface $node_storage) {
    $count = $node_storage->getQuery()
      ->condition('type', 'article')
      ->count()
      ->execute();
    $this->assertGreaterThan(0, $count);
    $nodes = $node_storage->loadByProperties(['title' => 'The azr guide to our favorite mushrooms']);
    $this->assertCount(1, $nodes);
    $node = reset($nodes);
    $this->assertStringContainsString('One of the best things about mushrooms is their versatility', $node->body->value);
  }

  /**
   * Assert block content are imported.
   *
   * @param \Drupal\Core\Entity\EntityStorageInterface $block_storage
   *   Block storage.
   */
  protected function assertImportedCustomBlock(EntityStorageInterface $block_storage) {
    $assert = $this->assertSession();
    foreach ($this->expectedBlocks() as $block_info) {
      $this->drupalGet($block_info['path']);

      // Verify that the block is placed.
      $assert->pageTextContains($block_info['unique_text']);

      // For blocks that have image alt text, also verify the presence of the
      // expected alt text.
      if (isset($block_info['image_alt_text'])) {
        $img_alt_text = $assert->elementExists('css', $block_info['image_css_selector'])->getAttribute('alt');
        $this->assertEquals($block_info['image_alt_text'], $img_alt_text);
      }

      // Verify that the block can be loaded.
      $count = $block_storage->getQuery()
        ->condition('type', $block_info['type'])
        ->count()
        ->execute();
      $this->assertGreaterThan(0, $count);
      $block = $block_storage->loadByProperties(['uuid' => $block_info['uuid']]);
      $this->assertCount(1, $block);
    }
  }

  /**
   * Returns the expected properties of this profile's custom blocks.
   */
  protected function expectedBlocks() {
    return [
      [
        'path' => '<front>',
        'type' => 'banner_block',
        'uuid' => '9aadf4a1-ded6-4017-a10d-a5e043396edf',
        'unique_text' => 'A wholesome pasta bake is the ultimate comfort food.',
        'image_css_selector' => '#block-azr-banner-home img',
        'image_alt_text' => 'Mouth watering vegetarian pasta bake with rich tomato sauce and cheese toppings',
      ],
      [
        'path' => '/vidyrabot',
        'type' => 'banner_block',
        'uuid' => '4c7d58a3-a45d-412d-9068-259c57e40541',
        'unique_text' => 'These sumptuous brownies should be gooey on the inside and crisp on the outside. A perfect indulgence!',
        'image_css_selector' => '#block-azr-banner-vidyrabot img',
        'image_alt_text' => 'A stack of chocolate and pecan brownies, sprinkled with pecan crumbs and crushed walnut, fresh out of the oven',
      ],
      [
        'path' => '/vidyrabot',
        'type' => 'disclaimer_block',
        'uuid' => '9b4dcd67-99f3-48d0-93c9-2c46648b29de',
        'unique_text' => 'is a fictional magazine and publisher for illustrative purposes only',
      ],
      [
        'path' => '/vidyrabot',
        'type' => 'footer_promo_block',
        'uuid' => '924ab293-8f5f-45a1-9c7f-2423ae61a241',
        'unique_text' => 'Magazine exclusive articles, vidyrabot and plenty of reasons to get your copy today.',
        'image_css_selector' => '#block-azr-footer-promo img',
        'image_alt_text' => '3 issue bundle of the AZR food magazine',
      ],
    ];
  }

}
