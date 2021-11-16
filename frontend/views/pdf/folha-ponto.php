<?php
use \common\components\Calendar;

$calendario = new Calendar();
?>
<style>
</style>
<table border="0" cellspacing="0" cellpadding="0" style="width:1546px;border:none;">
	<tr style="height:897.35px">
		<td style="width:1546px;padding:10px;">
			<table class="MsoTableGridLight" width="1546" border="0" cellspacing="0" cellpadding="0" style="border: none;">
				<tbody>
					<tr>
						<td width="773" style="border:none!important;padding:10px">
							<p><span style="font-size:18px"><strong>Condutor</strong><Br><?= $model->nome ?></span></p>
						</td>
						<td width="773" style="border:none;padding:20px;text-align:right;"><strong>Alvará N&ordm;</strong><Br><?= $model->alvara ?></p>
						</td>
					</tr>
				</tbody>
			</table>
			<p>&nbsp;</p>
			<p>&nbsp;</p>

			<table border="1" cellspacing="0" cellpadding="0" style="width: 100%;">
				<tbody>
					<tr>
						<td style="border:none;padding:60px;font-size: 25px;width: 100%">
							<p>*Srs. Condutores favor assinalar com um "X" no calendário somente nos dias trabalhados</p>
							<p>* Se houver sábado letivo, será obrigatório justificar o evento ocorrido.</p>
							<p>*
							Entregar no PRIMEIRO dia letivo do PRÓXIMO mês, com ocorrências se houver.</p>
							<?= $calendario->MostreCalendario(Date('m')) ?>
						</td>
					</tr>
				</tbody>
			</table>
			<p><span>&nbsp;</span></p>
			<table border="1" cellspacing="20" cellpadding="0" style="width: 100%;border-collapse:colapse;">
				<tr>
					<td style="width:25%;padding:10px;border:1px solid #222222;text-align: center;background: #CCCCCC;">
						<p><strong>Dias Letivos Trabalhados</strong></p>
					</td>
					<td style="width:25%;padding:10px;border:1px solid #222222;text-align: center;background: #CCCCCC;">
						<p><strong>Kms rodado por dia / N&ordm; de viagens por dia</strong></p>
					</td>
					<td style="width:25%;padding:10px;border:1px solid #222222;text-align: center;background: #CCCCCC;">
						<p><strong>Valor por Km / Valor por Viagem</strong></p>
					</td>
					<td style="width:25%;padding:10px;border:1px solid #222222;text-align: center;background: #CCCCCC;">
						<p><strong>Total (R$)</strong></p>
					</td>
				</tr>
				<tr>
					<td style="border:1px solid #222222;padding:10px">
						<p><span>&nbsp;</span></p>
					</td>
					<td style="border:1px solid #222222;padding:10px">
						<p><span>&nbsp;</span></p>
					</td>
					<td style="border:1px solid #222222;padding:10px">
						<p><span>&nbsp;</span></p>
					</td>
					<td style="border:1px solid #222222;padding:10px">
						<p><span>&nbsp;</span></p>
					</td>
				</tr>
				<tr>
					<td style="border:1px solid #222222;padding:10px">
						<p><span>&nbsp;</span></p>
					</td>
					<td style="border:1px solid #222222;padding:10px">
						<p><span>&nbsp;</span></p>
					</td>
					<td style="border:1px solid #222222;padding:10px">
						<p><span>&nbsp;</span></p>
					</td>
					<td style="border:1px solid #222222;padding:10px">
						<p><span>&nbsp;</span></p>
					</td>
				</tr>
			</table>

			<p><span>&nbsp;</span></p>
			<center><h3>Srs. Condutores preencher abaixo somente se houver trabalhado no sábado.</h3></center>
			<table style="width:100%;border: 1px solid #222222;border-collapse: collapse;">
				<tr>
					<td style="background: #CCCCCC;width:50%;">
						<strong>ESCOLA</strong>
					</td>
					<td style="background: #CCCCCC;width:50%;">
						<strong>JUSTIFICATIVA DO SÁBADO LETIVO</strong>
					</td>
				</tr>
				<tr>
					<td style="padding:10px;width: 50%"><p>&nbsp;</p></td>
					<td style="padding:10px;width: 50%"><p>&nbsp;</p></td>
				</tr>
				<tr>
					<td style="padding:10px;width: 50%"><p>&nbsp;</p></td>
					<td style="padding:10px;width: 50%"><p>&nbsp;</p></td>
				</tr>
				<tr>
					<td style="padding:10px;width: 50%"><p>&nbsp;</p></td>
					<td style="padding:10px;width: 50%"><p>&nbsp;</p></td>
				</tr>
				<tr>
					<td style="padding:10px;width: 50%"><p>&nbsp;</p></td>
					<td style="padding:10px;width: 50%"><p>&nbsp;</p></td>
				</tr>
			</table>

			<table style="border: 1px solid #222222;width: 100%;border-collapse: collapse;">
				<tr>
					<td style="width: 100%;text-align:left;">
						<strong>Ocorrências:</strong>
					</td>
				</tr>
				<tr>
					<td style="width: 100%;"><span>&nbsp;</span></td>
				</tr>
				<tr>
					<td style="width: 100%;"><span>&nbsp;</span></td>
				</tr>
				<tr>
					<td style="width: 100%;"><span>&nbsp;</span></td>
				</tr>
				<tr>
					<td style="width: 100%;"><span>&nbsp;</span></td>
				</tr>
			</table>

			<table style="border: 1px solid #222222;width: 100%;border-collapse: collapse;">
				<tr>
					<td style="width: 100%;border: none;">
						<center><strong>Carimbo das escolas atendidas</strong></center>
					</td>
				</tr>
				<tr>
					<td rowspan="5" style="width: 100%;padding: 450px;border: none;">
						<span>&nbsp;</span>
					</td>
				</tr>
			</table>

			<table border="0" valign="bottom" cellspacing="0" cellpadding="0" style="border:none;width: 100%;">
				<tr>
					<td style="width:30%;border:none;padding-top: 200px;">
						<p><strong><center>____ / ____ /________</center></strong></p>
						<p><strong><center>Data</center></strong></p>
					</td>
					<td style="width:40%;border:1px solid #222222;vertical-align: top;">
						<center><strong>Assinatura/Carimbo dos (as) Diretores (as)</strong></center>
					</td>
					<td style="width:30%;border:none;padding-top: 200px;">
						<p><strong><center>Assinatura do condutor</center></strong></p>
					</td>
				</tr>
			</table>
		</td>
	</tr>
</table>