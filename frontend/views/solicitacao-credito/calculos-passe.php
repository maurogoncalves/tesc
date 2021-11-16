<?php 
use common\models\SolicitacaoCredito;

?>


<div id="modal" class="modal fade" role="dialog">
    <div class="modal-dialog modal-lg">

        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">Informações do aluno</h4>
            </div>
            <div class="modal-body" id="informacoes">
                <div class="row">
                    <div class="col-md-12">
                        <label>CPF</label>
                        <div class="input-group">
                            <input type="text" id="cpf" class="form-control">
                            <span class="input-group-btn">
                                <button class="btn btn-default copy" type="button"><i class="far fa-copy"></i></button>
                            </span>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <label>Cartão de Vale Transporte</label>
                        <div class="input-group">
                            <input type="text" id="cartaoValeTransporte" class="form-control">
                            <span class="input-group-btn">
                                <button class="btn btn-default copy" type="button"><i class="far fa-copy"></i></button>
                            </span>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <label>Cartão de Passe Escolar</label>
                        <div class="input-group">
                            <input type="text" id="cartaoPasseEscolar" class="form-control">
                            <span class="input-group-btn">
                                <button class="btn btn-default copy" type="button"><i class="far fa-copy"></i></button>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Fechar</button>
            </div>
        </div>

    </div>
</div>
<script type="text/javascript">
var tipo = '<?=  $model->tipoSolicitacao == SolicitacaoCredito::TIPO_PASSE_ESCOLAR ? 'passeEscolar' : 'valeTransporte' ?>';
var valorAtualPasse = null;
var configuracoes = null;
$.getJSON("index.php?r=configuracao%2Fview-ajax")
.done(function(response) {
    if(tipo == 'passeEscolar') {
        valorAtualPasse = parseFloat(response.passeEscolar) * 2
    } else {
        valorAtualPasse = parseFloat(response.valeTransporte) * 2
    }
    configuracoes = response; 
});
$(document).ready(function() {
    calcSaldoDiasLetivos();
    calcValorNecessario();
    $(".a").click(function(event)  {
        event.preventDefault(); 
    })
    $('[data-toggle="tooltip"]').tooltip();

    $('.copy').on("click", function() {
        let el = $(this).parent().parent().find('input');
        el.select();
        document.execCommand('copy');
    });

    $(".consultarCredito").click(function() {
        // console.log(this);
        let idAluno = $(this).attr('aluno');
        $.get('index.php?r=aluno/aluno-ajax', {
            id: idAluno
        }).done((result) => {
            // console.log(result);
            $('#modal').modal("show");

            $("#cpf").val(result.cpf)
        });

        $.get('index.php?r=solicitacao-transporte/view-solicitacao-ajax', {
            id: idAluno
        }).done((result) => {
            // console.log(result);
            $('#modal').modal("show");
            $("#cartaoPasseEscolar").val(result.cartaoPasseEscolar)
            $("#cartaoValeTransporte").val(result.cartaoValeTransporte)
        });
    });

});

function inputAluno(element, flag) {

    $(element).closest('tr').find('.inputSaldoRestante').val("")
    $(element).closest('tr').find('.justificativa').val("")
    $(element).closest('tr').find('.valorNecessario').val("")
    $(element).closest('tr').find('.inputDiasLetivosFecharMes').val("")
    $(element).closest('tr').find('.inputSaldoRestante').prop('readonly', flag)
    $(element).closest('tr').find('.justificativa').prop('readonly', flag)
    // $(element).closest('tr').find('.valorNecessario').prop('readonly', flag)


    let divs = $(element).closest('tr').find(".habilitadoNecessidadeCredito");

    divs.each(function() {
        // console.log($(this))
        let aviso =  $(element).closest('tr').find(".avisoNecessidadeCredito");
        if(!flag){
            $(this).css('display', 'block') //to show
            $(aviso).css('display', 'none') //to show

        } else {
            $(this).css('display', 'none') //to show
            $(aviso).css('display', 'block') //to show
            
        }
    });
    calcSaldoDiasLetivos()
 
}
$("form").bind("keypress", function(e) {
    if (e.keyCode == 13) {
        return false;
    }
});
$(document).ready(function() {
    $('.money').mask('#.##0,00', {
        reverse: true
    });
});
$(".selecionarTodos").change(function(){
    let flag = $(this).prop('checked')
    $(".alunoMarcado").each(function() {
        $(this).prop('checked', flag)
    });
    processarCheckbox()
});
$("#qtdeAlunos").change(function()  {
    calcValorNecessarioTotal();
})
$(".alunoMarcado").click(function() {
    // processarCheckbox();
    let cont = 0;

$(".alunoMarcado").each(function() {
    if ($(this).prop('checked')) {
        cont++;
    }
})

// console.warn($(this).prop('checked'));
if ($(this).prop('checked')) {
    inputAluno($(this), false)

} else {
    inputAluno($(this), true)
}
$(".qtdeAlunos").val(cont)
calcValorNecessarioTotal();
})

$(".fundhas").click(function() {
    console.log('aaa')
    calcSaldoDiasLetivos()
    
})
function processarCheckbox(){
    let cont = 0;

    $(".alunoMarcado").each(function() {
        if ($(this).prop('checked')) {
            cont++;
            inputAluno($(this), false)
        } else {
            inputAluno($(this), true)
        }
    })

   
    $(".qtdeAlunos").val(cont)
    calcValorNecessario();
    calcValorNecessarioTotal();

}
function BRLtoReal(valor){
    if(valor === ""){
        valor =  0;
    }else{
        valor = valor.replace(".","");
        valor = valor.replace(",",".");
        valor = parseFloat(valor);
    }
    return valor;
}

 function realToBRL(numero){
    var numero = numero.toFixed(2).split('.');
    numero[0] = numero[0].split(/(?=(?:...)*$)/).join('.');
    return numero.join(',');
}
$("#diasLetivosMes").change(function(){
    calcValorNecessarioTotal();
        atualizarSaldo();
    calcValorNecessario();
    setTimeout(() =>    calcValorNecessario(), 500)
})
// Recalcula saldo restante nos cartões
$(".inputSaldoRestante").change(function() {
    atualizarSaldo();
    calcValorNecessario();
    setTimeout(() =>    calcValorNecessario(), 500)
});
$("#diasLetivosMes").change(function() {
    let value = BRLtoReal($(this).val())
    let total = value * valorAtualPasse;
    console.warn('diasLetivosMes', realToBRL(total))
});

$("#saldoRestanteEscola").change(() => {
    console.warn('saldoRestanteEscola')
    calcValorSerCreditado();
});
$("#diasLetivosRestantes").change(function() {
    console.warn('diasLetivosRestantes')
    calcSaldoDiasLetivos();
    
});
$("#saldoRestanteCartoes").change(() => {
    console.warn('saldoRestanteCartoes')
    calcValorSerCreditado();
});

function calcValorSerCreditado(){
    
    console.warn('calcValorSerCreditado')
    let total = 0;
    let valorSaldoRestanteEscola = BRLtoReal(saldoRestanteEscola.value);
    let valorSaldoRestanteCartoes = BRLtoReal(saldoRestanteCartoes.value);
    let totalNecessario = BRLtoReal(valorNecessarioTotal.value)
    total = totalNecessario - (valorSaldoRestanteEscola + valorSaldoRestanteCartoes)
    $("#valorCreditado").val(realToBRL(total))
    // console.warn('valorCreditado')
}
 function calcSaldoDiasLetivos(){
    ///Dias letivos restantes para fechar o mês / Saldo descontado
   let = diasLetivosRestantes =  parseInt($("#diasLetivosRestantes").val())
    $(".inputDiasLetivosFecharMes").each(function() {
        // CHECK IF IS FUNDHAS
           
        let saldoDescontado = diasLetivosRestantes * valorAtualPasse
        if($(this).closest('tr').find('.fundhas').prop('checked')){
            saldoDescontado *= 2;
        }
        $(this).val(saldoDescontado)
    });
    
    calcAntiUE();
    calcValorNecessario();
}
  function calcAntiUE(){
    console.warn('calcAntiUE')
    // Anti UE É o saldo atual - descontado
     let divs = $(".inputAntiUe");

     for(let i = 0; i < divs.length; i++){
         let item = divs[i];
         let totalAntiUe = 0;
        let saldoRestante = BRLtoReal($(item).closest('tr').find('.inputSaldoRestante').val())
        let saldoDescontado = $(item).closest('tr').find('.inputDiasLetivosFecharMes').val()
        // console.log(saldoRestante, saldoDescontado)
        // if(saldoRestante || saldoRestante == 0)
            totalAntiUe = saldoRestante - saldoDescontado
        $(item).val(totalAntiUe)

        // CALCULO DO SALDO NO FINAL DO MÊS
        let saldoFinalMes = 0
        
        if(totalAntiUe >= 0) 
           saldoFinalMes = totalAntiUe
        $(item).closest('tr').find('.saldoFinalMes').val(saldoFinalMes)

     }

}
async function calcValorNecessario(){
    console.warn('calcValorNecessario')
    ///VALOR A SER CREDITADO
    let saldoNecessario = parseInt(diasLetivosMes.value) * valorAtualPasse

    let divs = $(".saldoFinalMes");

    for(let i = 0; i < divs.length; i++){
        let item = divs[i];
        let saldoAtual = parseFloat($(item).val())
        let totalSaldo = saldoNecessario - saldoAtual
        // console.log(saldoNecessario, saldoAtual)
     
      
        // console.log('totalSaldo', totalSaldo)
        $(item).closest('tr').find('.valorNecessario').val(totalSaldo)
        // console.error($(item).closest('tr').find('.valorNecessario').val())
        if(totalSaldo > 0){
            $(item).closest('tr').find('.checkboxNecessidadeCredito').attr('checked', true)   
        } else {
            $(item).closest('tr').find('.checkboxNecessidadeCredito').attr('checked', false)
        }
    } 
    atualizarSaldo();
    calcValorSerCreditado()
    
}
 function atualizarSaldo(){
    console.warn('AtualizarSaldo')
    let total = 0;
 
    montarCalculos();
    $(".saldoFinalMes").each(function() {
        total += parseFloat($(this).val());
        // $(this).css('display', 'block') //to show
    });
    // console.log(total)
    $("#saldoRestanteCartoes").val(realToBRL(total))
    
}


function montarCalculos(){
    calcValorSerCreditado();
    calcAntiUE();
    // calcValorNecessario();
}


function calcValorNecessarioTotal(){
    let totalDiasLetivosMes = parseInt(diasLetivosMes.value);
    let totalQtdeAlunos = parseInt(qtdeAlunos.value)
    // console.error(totalDiasLetivosMes, totalQtdeAlunos)
    let valorNecessarioTotal = totalDiasLetivosMes * totalQtdeAlunos * valorAtualPasse;
    $("#valorNecessarioTotal").val(realToBRL(valorNecessarioTotal))
    calcValorSerCreditado()
}

$("#salvar").click(function(ev) {
    event.preventDefault(); 
    return Swal.fire({
        icon: 'warning',
        title: 'Atenção!',
        html: "Não será possível editar a solicitação, tem certeza que deseja continuar?",
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Sim',
        cancelButtonText: 'Não'
    }).then((result) => {
        if (result.value) {
            $( "#formCredito" ).submit();
            console.log(1)
        }
    });
});
</script>

<?php if(isset($_GET['debug'])): ?>
<script>
        $('.debug').show();

</script>
<?php endif; ?>