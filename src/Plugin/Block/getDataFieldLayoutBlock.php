<?php

namespace Drupal\get_data_field_from_url\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Entity\EntityInterface;

/**
 * Provides an example block.
 *
 * @Block(
 *   id = "get_data_field_from_url_field",
 *   admin_label = @Translation(" get data field Layout "),
 *   category = @Translation("get data field from url")
 * )
 */
class getDataFieldLayoutBlock extends BlockBase {


  /**
   *
   * {@inheritdoc}
   */
  public function defaultConfiguration() {
    return [
      'field_name' => '',
      'field_formatter' => ''
    ];
  }

  /**
   *
   * {@inheritdoc}
   */
  public function blockForm($form, FormStateInterface $form_state) {
    $form['field_name'] = [
      '#type' => 'textfield',
      '#title' => $this->t(' Nom du champs '),
      '#default_value' => $this->configuration['field_name']
    ];
    $form['field_formatter'] = [
      '#type' => 'textfield',
      '#title' => $this->t(' id du formatter à utiliser '),
      '#default_value' => $this->configuration['field_formatter']
    ];
    return $form;
  }

  /**
   *
   * {@inheritdoc}
   */
  public function blockSubmit($form, FormStateInterface $form_state) {
    $this->configuration['field_name'] = $form_state->getValue('field_name');
    $this->configuration['field_formatter'] = $form_state->getValue('field_formatter');
  }

  /**
   *
   * {@inheritdoc}
   */
  public function build() {
    $route_name = \Drupal::routeMatch()->getRouteName();
    $route_match = \Drupal::routeMatch()->getParameters()->all();
    $field_name = $this->configuration['field_name'];
    $field_formatter = $this->configuration["field_formatter"];
    $entity = null;
    if ($route_name ==  "entity.taxonomy_term.canonical") {
      /**
       * @var \Drupal\taxonomy\Entity\Term
       */
      $entity = $route_match["taxonomy_term"];
    } else {
      /**
       *
       * @var \Drupal\node\Entity\Node $entity
       */
      $entity = reset($route_match);
    }
    if (!empty($entity) && $entity instanceof EntityInterface) {
      if ($entity->hasField($field_name)) {
        /**
         *
         * @var \Drupal\Core\Field\FieldItemList $field
         */
        $field = $entity->{$field_name};
        if ($field_formatter) {
          $view_builder = \Drupal::entityTypeManager()->getViewBuilder($entity->getEntityTypeId());
          $view = $view_builder->viewField($field, [
            'label' => 'hidden',
            'type' => $field_formatter, // Set the formatter here
            'settings' => [],
            'weight' => 0,
          ]);
          return $view;
        }
        return $field->view([
          'label' => 'hidden'
        ]);
      }
    }
    $build['title'] = [
      '#type' => 'html_tag',
      '#tag' => 'h1',
      '#value' => "mjkfjj ikuj"
    ];
    return $build;
  }
}
