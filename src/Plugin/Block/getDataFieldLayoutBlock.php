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
      'field_formatter' => '',
      'show_label' => false
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
    $form['show_label'] = [
      '#type' => 'checkbox',
      '#title' => $this->t(' Show label '),
      '#default_value' => $this->configuration['show_label']
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
    $this->configuration['show_label'] = $form_state->getValue('show_label');
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
    $show_label = $this->configuration["show_label"];
    $entity = null;
    if ($route_name == "entity.taxonomy_term.canonical") {
      /**
       *
       * @var \Drupal\taxonomy\Entity\Term
       */
      $entity = $route_match["taxonomy_term"];
    }
    else {
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
        $display_options = [
          'label' => $show_label ? 'above' : 'hidden'
        ];
        if ($field_formatter) {
          $display_options += [
            'type' => $field_formatter,
            'settings' => [],
            'weight' => 0
          ];
        }
        return $field->view($display_options);
      }
    }
    return [];
  }
}
