$(document).on('click', '.ajouter', function(e){

    const titre =  $(this).data('titre');
    const type = $(this).data('type');
    const titred = titre.split(" ");
    const afair =  titred[0]+ " du :";
    //insertion
    $('#seanTraveau').text(titre);
    $('#titreajou').text(afair);
    $('.fonction').text(titred[0]);
    $('#type-work').val(type);
    effacer($("#add-works"))
    $('#ajouter').modal({backdrop: true});
})

$("#add-works").submit(function(e){
    e.preventDefault();
    serialiseform($(this))
        .then(data => {
            $('#conteneur').val('');
            $('#conteneur').code('');
            $('#ajouter').modal('toggle');
            if(data.id){
                $.get('/works/table/' + data.classe_id, html => {
                    $('#table-works-body').html(html)
                })
                //$('#table-works-body').append(html_renderer(data))
            }
        }).catch(error => {
        notify('error', displayErrors(error.responseJSON))
    })
});

$(document).on('click', '.showtravail', function() {
    const title =  $(this).data("intutiler");
    const content = $(this).data("link");
    const datetra = $(this).data("date");
    const charge = $(this).data("status");
    const type = $(this).data("type");
    const file = $(this).data("file");
    const file_type = $(this).data("file-type");
    const $header = $('#exampleModalLabel');
    const body = $('#contenerTravail');
    let $html;
    $header.css('color', '#fff');
    $html = translate_type(type);
    $html += datetra.slice(0, -3) ;
    $html += " " ;
    $html += switch_charge(charge)
    $header.html( $html);
    if(content !== null){
        $.get( `/storage/${content}/index.html`, function( data ) {
            let cont = `<h1>${title}</h1>`
                cont += data
            body.html(cont);
            if(file !== undefined){
                const $link = ` <a href="/storage/${content}/${file}" id="lienfile" target="_blank"> ${title} ${file_type}</a>`
                body.append($link);
            }
        });
    }
    $("#showTraveau").modal({backdrop: true});
}); // showedite


$(document).on('click', '.showedite', function(){
    var id , datetra, charge,matier;
    const title =  $(this).data("intutiler");
    const content = $(this).data("link");
    const file = $(this).data("file");
    const type = $(this).data("type");
    const file_type = $(this).data("file-type");
    const $form =  $('#add-works');
    datetra = $(this).data("date");
    charge = $(this).data("status");
    matier = $(this).data("matiere");
    id = $(this).data("id");
    $("#id-work").val(id);
    $("#type-work").val(type);
    $('#matiere_id option[value='+matier+']').prop('selected', true);
    $('#charge-work option[value='+charge+']').prop('selected', true);
    $('#date_to_return').val(datetra);
    $('#intutiler').val(title);
    $form.attr('action', `/works/${id}/update`)
    //$form.attr('method', 'POST')
    if(content !== undefined){
        $.get( `/storage/${content}/index.html`, function( data ) {
            $('.summernote').code(data);
            $('#conteneur').val(data);
        });
    }
    if(file !== undefined){
        const $html = $(`<a href="/storage/${content}/${file}" target='_blank'>${title} ${file_type}</a>`)
        $('#files').html($html).fadeIn();
    }
    $("#ajouter").modal({backdrop: true});
});

$(document).on('click', '.reset-works', function(){
    $('.summernote').code('');
    $('#conteneur').val('');
    const $form = $("#add-works");
    $form.attr('action', '/works')
    effacer($form);
    $('#files').html('').fadeOut();
    $("#ajouter").modal('toggle');
})


$('.all-checkbox-works').change(function (e){
    if($(this).is(':checked')){
        $('.deleted-checked').attr('checked', true)
    }else{
        $('.deleted-checked').attr('checked', false)
    }
})




$('.delete-works').click(function(e){
    e.preventDefault();
    $("input[name=id-work]:checked").each(
        function() {
            if($(this).is(':checked')){
                const id = $(this).data("id");
                has_delete(id, `/works/${id}`)
                    .then(() => {
                        $('#deleted-' + id).remove()
                    })
            }
        }
    );
    $('.all-checkbox-works').attr('checked', false)
});
/*$(document).ready(function () {

})*/
$('.summernote').summernote({
    height: 150,   //set editable area's height
    codemirror: { // codemirror options
        theme: 'monokai'
    }
});

$(document).on('click', '.link-week', function (e){
    $('.link-week').removeClass('active')
    $(this).addClass('active')
    const date = $(this).data('date')
    $.get(`/works/tree/${encodeURIComponent(date)}`)
})


$(".date_to_return_picker").datetimepicker({
    regional: "fr",
    showOtherMonths: true,
    selectOtherMonths: true,
    changeMonth: true,
    dateFormat: 'yy-mm-dd',
    dayNamesMin: ['Di', 'Lu', 'Ma', 'Me', 'Je', 'Ve', 'Sa']
});

function switch_charge(charge){
    switch (charge) {
        case 1:
            return '<span class="badge badge-success">Charge  légère</span>'
        case 2:
            return '<span class="badge badge-warning">Charge  moyenne</span>'
        case 3:
            return '<span class="badge badge-danger">charge importante</span>'
        default:
            return '<span class="badge badge-info">Charge  légère no definit</span>'
    }
}

function translate_type(data){
    switch (data) {
        case 'sitting':
            return 'Séance pour '
        case 'work':
            return 'Travaux pour '
        case 'control':
            return 'Controle pour '
        default:
            return 'Travaux pour '
    }
}

function html_renderer(notebook){

    return `
         <tr class="tdata-lgn" id="deleted-${ notebook.id }">
                                <td class="cel-selection">
                                    <input type="checkbox" name="id-work" data-id="${ notebook.id }" class="deleted-checked">
                                </td>
                                <td>
                                    <button class="btn btn-success" title="Modifier la date de visibilité">
                                        <i class="icon-eye-open "></i>
                                        <i class="icon-eye-close hidden "></i>
                                    </button>
                                </td>

                                <td >
                                    <button class="btn btn-default showtravail"
                                            data-intutiler="${ notebook.intutiler }"
                                            data-link="${ notebook.content }"
                                            data-type="${ notebook.type }"
                                            data-date="${ notebook.date_return }"
                                            data-status="${ notebook.charge }"
                                            data-file="${ notebook.fichier }"
                                          
                                    >
                                        <i class="icon-folder-open"></i>
                                    </button>
                                </td>
                                <td  class="cel-retour-eleve">
                                    <button class="btn btn-danger listeDeposer" title="Consulter le travail des élèves (fermé)">
                                        <i class="icon-inbox"></i><span>0/12</span>
                                    </button>
                                </td>
                                <td >
                                    <!--<button class="btn btn-danger"  title="Retard interdit">
                                        <i class="icon-time"></i>
                                    </button>-->
                                </td>
                                <td ></td>
                                <td class="cel-classe"  title="${ notebook.type }">${ notebook.charge_type }</td>
                                <td class="text-uppercase bg-pink bg-lg">${ notebook.matter.matiere }</td>
                                <td >
                                    <a href="#"
                                       class="btn btn-default showedite"
                                         data-id="${ notebook.id }"
                                           data-intutiler="${ notebook.intutiler }"
                                           data-link="${ notebook.content }"
                                           data-original-title="${ notebook.intituler }"
                                           data-status="${ notebook.charge }"
                                           data-date="${ notebook.date_to_return }"
                                           data-matiere="${ notebook.matiere_id }"
                                           data-file="${ notebook.fichier }"
                                           data-type="${ notebook.type }"
                                           
                                            title="Modifier le travail"
                                    >
                                        <i class="icon-edit"></i>
                                    </a>
                                </td>
                                <td >
                                    <button class="btn btn-default"  title="Copier le travail">
                                        <i class="icon-copy"></i>
                                    </button>
                                </td>
                                <td >
                                <a  href="#" class="showtravail"
                                        data-intutiler="${ notebook.intutiler }"
                                        data-link="${ notebook.content }"
                                        data-type="${ notebook.type }"
                                        data-date="${ notebook.date_return }"
                                        data-status="${ notebook.charge }"
                                        data-file="${ notebook.fichier }"
                                       
                                    >
                                        ${ notebook.intutiler.length > 20 ? notebook.intutiler.slice(0, 20) + '...' : notebook.intutiler }
                                    </a>
                                </td>
                                <td> 
                                   <span class="petit">(<small class="text-muted">${ notebook.dp }</small>)</span>
                                </td>
                                <td>
                                    ${ notebook.fichier ?
                                       ` <a href="/storage/${ notebook.content }/${ notebook.fichier }" target="_blank" >
                                            ${notebook.file_type}
                                        </a>` : ''
                                    }
                                </td>
                            </tr>
    `
}