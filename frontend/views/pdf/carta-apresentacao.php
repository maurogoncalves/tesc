<?php

use yii\helpers\Html;
use yii\helpers\Url;
use \common\components\Calendar;

/* @var $this yii\web\View */
/* @var $model common\models\Ponto */

$calendario = new Calendar();

?>

<div class="row" id="pdf">
	<div class="col-md-12">
		<div class="box box-solid">
			<div class="box-body">
				<table>
					<tr class="pdf-header">
						<td class="logo">
							<img src="img/brasao.png" width="100" />
						</td>
						<td class="titulo">
							<h4>PREFEITURA DE SÃO JOSÉ DOS CAMPOS<br>
							SECRETARIA DE EDUCAÇÃO E CIDADANIA<br>
							SETOR DE TRANSPORTE ESCOLAR</h4>
						</td>
					</tr>
				</table>
				
				<table>
					<tr>
						<td class="conteudo" align="right">
							<p>São José dos Campos, <?= date('d') ?> de <?= $calendario->GetNomeMes(date('m')) ?> de <?= date('Y') ?>.</p>
						</td>
					</tr>
					<tr>
						<td class="conteudo" align="right">
							&nbsp;
						</td>
					</tr>
					<tr>
						<td class="conteudo" align="right">
							<p>À direção da <b><?= $escola->nomeCompleto ?></b></p>
						</td>
					</tr>
					<tr>
						<td>
							<p>&nbsp;</p>						
							<p>&nbsp;</p>
							<p>&nbsp;</p>
							<p style="font-size: 19px;">Declaro para os devidos fins que o (a) condutor (a) <b><?= $condutor->nome ?></b>, Alvará <b><?= $condutor->alvara ?></b>, é contratado (a) da Secretaria de Educação e Cidadania do município de São José dos Campos, e a partir da data de hoje fará o transporte dos alunos atribuídos a ele (a) na plataforma “Transporte Escolar SJC”.
							</p>
							<p>&nbsp;</p>
							<p>&nbsp;</p>
							<p>&nbsp;</p>
							<p>&nbsp;</p>
							<p>&nbsp;</p>
						</td>
					</tr>
					<tr>
						<td><b>__________________________________________________________________________________________________________________________</b></td>
					</tr>
					<tr>
						<td align="center"><h4><b>Setor de Transporte Escolar – SEC</b></h4></td>
					</tr>
				</table>

				<p>&nbsp;</p>
				<p>&nbsp;</p>
				<p>&nbsp;</p>
				<p>&nbsp;</p>
				<p>&nbsp;</p>
				<p>&nbsp;</p>
				<p>&nbsp;</p>
				<p>&nbsp;</p>
				<p>&nbsp;</p>
				<p>&nbsp;</p>
				<p>&nbsp;</p>
				<p align="right">VIA DA UNIDADE ESCOLAR</p>
		
				<pagebreak />

				<table>
					<tr class="pdf-header">
						<td class="logo">
							<img src="img/brasao.png" width="100" />
						</td>
						<td class="titulo">
							<h4>PREFEITURA DE SÃO JOSÉ DOS CAMPOS<br>
							SECRETARIA DE EDUCAÇÃO E CIDADANIA<br>
							SETOR DE TRANSPORTE ESCOLAR</h4>
						</td>
					</tr>
				</table>
				
				<table>
					<tr>
						<td class="conteudo" align="right">
							<p>São José dos Campos, <?= date('d') ?> de <?= $calendario->GetNomeMes(date('m')) ?> de <?= date('Y') ?>.</p>
						</td>
					</tr>
					<tr>
						<td class="conteudo" align="right">
							&nbsp;
						</td>
					</tr>
					<tr>
						<td class="conteudo" align="right">
							<p>À direção da <b><?= $escola->nomeCompleto ?></b></p>
						</td>
					</tr>
					<tr>
						<td>
							<p>&nbsp;</p>						
							<p>&nbsp;</p>
							<p>&nbsp;</p>
							<p style="font-size: 19px;">
							Declaro para os devidos fins que o (a) condutor (a) <b><?= $condutor->nome ?></b>, Alvará <b><?= $condutor->alvara ?></b>, é contratado (a) da Secretaria de Educação e Cidadania do município de São José dos Campos, e a partir da data de hoje fará o transporte dos alunos atribuídos a ele (a) na plataforma “Transporte Escolar SJC”.
							</p>
							<p>&nbsp;</p>
							<p>&nbsp;</p>
							<p>&nbsp;</p>
							<p>&nbsp;</p>
							<p>&nbsp;</p>
						</td>
					</tr>

					<tr>
						<td><b>__________________________________________________________________________________________________________________________</b></td>
					</tr>
					<tr>
						<td align="center"><h4><b>Setor de Transporte Escolar – SEC</b></h4></td>
					</tr>
				</table>

				<table class="recibo">
					<tr>
							<td>
								<center><h4><b>PROTOCOLO DE RECIBO</b></h4></center>
								<p>&nbsp;</p>
								<p>&nbsp;</p>
							</td>
					</tr>
					<tr>
						<td align="left" style="padding:3px;">
							Eu, ________________________________________________________, funcionário (a) da <b><?= $escola->nomeCompleto ?></b>, recebi na data de hoje, a carta de apresentação do (a) condutor (a) <b><?= $condutor->nome ?></b>, que iniciará o transporte a partir de <?= date('d') ?> de <?= $calendario->GetNomeMes(date('m')) ?> de <?= date('Y') ?>. Ressalto que a Unidade Escolar está ciente que os dados dos alunos atendidos podem ser consultados na Plataforma “Transporte Escolar SJC”
						</td>
					</tr>
					<tr>
						<td>
						<p>&nbsp;</p>
							<p>São José dos Campos, ____ de ___________________________________________ de <?= date('Y') ?>.</p>
							<p>&nbsp;</p>
							<p>&nbsp;</p>
							<p><center>____________________________________________</center></p>
							<p><center>Assinatura e Carimbo</center></p>
						</td>
					</tr>
				</table>
				<br><br><br><br><br>
				<p align="right">VIA DO SETOR DE TRANSPORTE ESCOLAR</p>
			</div>
		</div>
	</div>
</div>


