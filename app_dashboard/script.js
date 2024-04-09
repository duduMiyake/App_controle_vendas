$(document).ready(() => {
	
    $('#documentacao').on('click', () => {
        // $('#pagina').load('documentacao.html')

        // $.get('documentacao.html', data => {
        //     $('#pagina').html(data)
        // })

        $.post('documentacao.html', data => {
            $('#pagina').html(data)
        })
    })

    $('#suporte').on('click', () => {
        // $('#pagina').load('suporte.html')

        // $.get('suporte.html', data => {
        //     $('#pagina').html(data)
        // })

        $.post('suporte.html', data => {
            $('#pagina').html(data)
        })
    })

    //ajax 
    $('#competencia').on('change', e => {

        let competencia = $(e.target).val()

        $.ajax({
            type: 'GET',
            url: 'app.php',
            data: `competencia=${competencia}`,
            dataType: 'json',
            success: dados => { 
                $('#numero_vendas').html(dados.numero_vendas)
                $('#total_vendas').html(dados.total_vendas)
                $('#total_clientes_ativos').html(dados.clientes_ativos)
                $('#total_clientes_inativos').html(dados.clientes_inativos)
                $('#total_reclamacoes').html(dados.total_reclamacoes)
                $('#total_elogios').html(dados.total_elogios)
                $('#total_sugestoes').html(dados.total_sugestoes)
                $('#total_despesas').html(dados.total_despesas)
             },
            error: erro => { console.log(erro) }
        })

        //metodo, url, dados, sucesso, erro
    })

})