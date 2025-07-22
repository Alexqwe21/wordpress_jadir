<?php


function handel_custom_checkout($fields) {
  // Mudar Campos

  var_dump($fields);
  exit;
  $fields['billing']['billing_phone']['required'] = true;
  unset($fields['billing']['billing_phone']);

  // Adicionar Campo Novo
  $fields['billing']['billing_presente'] = [
    'label' => 'Embrulhar para Presente?',
    'required' => false,
    'class' => ['form-row-wide'],
    'clear' => true,
    'type' => 'select',
    'options' => [
      'nao' => 'Não',
      'sim' => 'Sim'
    ]
  ];
  return $fields;
}
add_filter('woocommerce_checkout_fields' , 'handel_custom_checkout');


?>