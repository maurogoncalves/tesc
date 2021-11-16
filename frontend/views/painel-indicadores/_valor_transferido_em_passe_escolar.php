<div class="box box-solid">
  <div class="box-header with-border">
    <h4><?= $this->title ?></h4>
  </div>

  <div class="box-body">
    <?php Pjax::begin(); ?>
      <?= GridView::widget([
        'dataProvider' => new ArrayDataProvider([
          'allModels' => $solicitacoes,
          'sort' => [
              'attributes' => [
                'idEscola',
                'status',
                'inicio',
                'fim',
                'criado',
                'creditoAdministrativo',
                'total'
              ],
          ],
          'pagination' => [
              'pageSize' => 10,
          ],
        ]),
        'columns' => [
          [
            'attribute' => 'idEscola',
            'value' => 'escola.nome'
          ],
          'status',
          [
            'attribute' => 'inicio',
            'value' => function ($model) {
              $data = explode('-', $model->inicio);
              return $data[2] . '/' . $data[1] . '/' . $data[0];
            }
          ],
          [
            'attribute' => 'fim',
            'value' => function ($model) {
              $data = explode('-', $model->fim);
              return $data[2] . '/' . $data[1] . '/' . $data[0];
            }
          ],
          [
            'attribute' => 'criado',
            'value' => function ($model) {
              $dateTime = explode(' ', $model->criado);
              $data = explode('-', $dateTime[0]);
              $hora = $dateTime[1];
              return $data[2] . '/' . $data[1] . '/' . $data[0] . ' ' . $hora;
            }
          ],
          'creditoAdministrativo',
          [
            'attribute' => 'total',
            'label' => 'Total',
            'value' => function ($model) {
              if ($model->solicitacaoCreditoAlunos) {
                $valor = 0;
                foreach ($model->solicitacaoCreditoAlunos as $key => $value) {
                  $valor += $value->valor;
                }
                return $valor;
              } else {
                return 0;
              }
            }
          ]
        ],
      ]); ?>
    <?php Pjax::end(); ?>
  </div>
</div>
