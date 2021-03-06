@include('admin.partials.selectors.default', [
    'selector' => [
      'id'        => 'role-type-selector',
      'class'     => 'btn-default',
      'icon'      => 'fa fa-briefcase',
      'current'   => request()->role_name,
      'values'    => $roles ?? App\Models\Role::toSelect('Hepsi', null),
      'default'   => 'Görev',
      'parameter' => 'role_name'
    ]
  ])
