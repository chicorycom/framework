/**
 *
 * @type {*|jQuery}
 */
const periode = $("#periodeDevoir").val();

/**
 *
 * @type {*|jQuery|HTMLElement}
 */
const downloadButton = $( ".transcript-show" );


/**
 *
 * @type {*|jQuery|HTMLElement}
 */
const $modal = $("#show-emploidu-temps");


/**
 *
 * @type {*|jQuery|HTMLElement}
 */
const $modalContent = $('#modal-content');

/**
 *
 * @param downloadButton
 */
function showBulletin(downloadButton){
    downloadButton.trigger( "focus" );
    const classe = downloadButton.attr('rel');
    const campus = downloadButton.data('campus');
    const $iframe = iframe(`/grades/${campus}/${classe}/${periode}`)
    $modalContent.empty().html($iframe);
    $iframe.hide()
    $modalContent.append('<div class="content-loader"><div class="loader"></div></div>');
    $iframe.load(function(){
        $(this).show();
        $modalContent.find('.content-loader').remove()
    });
    $modal.modal({backdrop: true});
}

/**
 * Add new devoir
 * show form
 */
$('.btn-nouveau-devoir').click(function(){
    const $btn = $("[data-btn='isBnt']");
    $btn.text('Nouveau Devoire')
    if($(".saisirDevoir").is(":hidden")){
        $btn.text('Fermer')
    }
    $(".saisirDevoir").toggle('slow');
    $("#desc-cart_rule-save").toggle('slow');
})


/**
 * saisie form
 */
$("#add-new-devoir").submit(function(e){
    e.preventDefault();
    const $form = $(this);
   // const $content_loading = $('#cart_rule_informations');
    $.LoadingOverlay("show");
    serialiseform($form)
        .then(r => {
            const $btn = $("[data-btn='isBnt']");
            $btn.text('Nouveau Devoire')
            $(".saisirDevoir").toggle('slow');
            $("#desc-cart_rule-save").toggle('slow');
            $.LoadingOverlay("hide");
        }).catch(error => {
        // console.log(error.responseJSON)
        $.LoadingOverlay("hide");
        $form.closest('.form-group').addClass('has-error');
        notify('error', displayErrors(error.responseJSON))
        //console.log(displayErrors(error.responseJSON))
    })
});
/**
 * end add new devoir
 */


/**
 * Edit notes update
 * Show edit Devoir
 */
$('.modiffierDevoir').click(function (e) {
    e.preventDefault();
    var classe,devoir,matiere,period,slug,titre, campus;
    classe = $(this).data('classe');
    devoir = $(this).data('id');
    matiere = $(this).data('matiere');
    period = $(this).data('period');
    campus = $(this).data('campus');
    var type = $(this).data('type');
    titre = "Devoir "+$(this).data('title')+" N° "+$(this).data('number') +" du "+$(this).data('date')+"<span class='pull-right' style='color: white'> Type :"+type+"</span>";
    slug = `${devoir}_${matiere}_${period}_${type}`;
    $('.titreDevoir').css('color', 'white').html(titre);
    htmlContentTask(campus, classe, slug)
   /*
    $( "<div></div>", {
        "class": "row",
        "id": "contener"
    }).appendTo( "#contenerTravail" );
    $( "<div></div>", {
        "class": "col-md-8 well",
        "id": "contenerDevoirs"
    }).appendTo( "#contener" );
    $( "<div></div>", {
        "class": "col-md-4 pull-right well",
        "id": "moyenMaxDevoirs"
    }).appendTo( "#contener" );
    notedevoir = "<p><strong>Baréme :</strong> 20</p></br>";
    notedevoir += "<p><strong>Note moyenne :</strong> <span id='notemoy'></span></p>";
    notedevoir += "<p><strong>Note maximale :</strong> <span id='notemax'></span></p>";
    notedevoir += "<p><strong>Note minimale :</strong> <span id='notemin'></span></p>";

    content = "<table class='table table-hover  devoirs'>";
    content += "<thead><tr class='active'><th >Etudiants</th><th >Notes</th></thead>";
    content += "<tbody>";
    $.getJSON( `/devoir/${campus}/${classe}/${slug}`, function( itemData ) {
        itemData[0].forEach(item => {
            content += '<tr><td> '+ item.students.prenom +' ' + item.students.nom + ' </td><td class="modifierdevoirid" data-list-id="'+item.id+'"  contenteditable>'+item.notes +' </td></tr>';
        })

        content +="</tbody></table>";
        $('#contenerDevoirs').html(content);
        $('#moyenMaxDevoirs').html(notedevoir);
        //$('.titreDevoir').addClass('text-primary');
        $('.titreDevoir').css('color', 'white').html(titre);
        $('#notemoy').text(itemData[1].moy);
        $('#notemax').text(itemData[1].max);
        $('#notemin').text(itemData[1].min);
        $("#showTraveau").modal({backdrop: true});
    }).then(()=>{

    }, (error)=>{notify('error', displayErrors(error.responseJSON))});
    */

})




/**
 * end Edit devoir
 */


/**
 * Delete Devoir
 */

$('.delete-devoir').click(function (e) {
    e.preventDefault();
    const $this = $(this);
    var classe,matiere,period,data,campus;
    const nbdevoir = $this.data('id');
    classe = $this.data('classe');
    matiere = $this.data('matiere');
    period = $this.data('period');
    campus = $this.data('campus');
    data = {campus, classe, nbdevoir, matiere, period, ...window.csrf};
    $("body").append("<div id=\"dialog-confirm\" title=\"Êtes-vous sûr de vouloir supprimer  ? \" style='display:none'>Attention si vous supprimez tous les notes de cette devoir enregistrés  seront supprimés définitivement. </div>")
    $("#dialog-confirm" ).dialog({
        resizable: false,
        height: "auto",
        width: 400,
        modal: true,
        buttons: {
            "OK": function() {
                $.ajax({
                    url:  `/devoir/${nbdevoir}/delete`,
                    type: 'DELETE',
                    data: data,
                    dataType:   'json',
                    success(){
                        notify('success',' Devoir Supprimer !!!');
                        $this.parent().parent().remove()
                    },
                    error(error){
                        notify('error ',` ${error.statusText} !!! code: ${error.status}`)
                        console.error(error)
                    }
                });
                $( this ).dialog( "close" );
            },
            Annuler: function() {
                $( this ).dialog( "close" );
            }
        }
    });
});
/**
 * end delete devoir
 */




/**
 * Show Emplois du temps
 */
$("#box-show-emploidutemps").click(function(){
    const classe = $(this).data('classe');
    const campus = $(this).data('campus');
    const $iframe = iframe(`/timetable/${campus}/${classe}`)

    $modalContent.html($iframe);
    $modal.modal({backdrop: true});
});

/*
   traveau a faire
    */



$(".datetime_picker").datetimepicker({
    regional:  "fr",
    showOtherMonths: true,
    selectOtherMonths: true,
    changeMonth: true,
    dayNamesMin: ['Di','Lu','Ma','Me','Je','Ve','Sa'],
});



$("#fileupload").change(function(){
    var nomfile = $('#fileupload').val();
    $('#files').html("<i class='fa fa-fw fa-paperclip text-muted'></i>"+nomfile);
    $('#files').show('slow');
});

$( ".datepickerDevoir" ).datepicker( {
    regional:  "fr",
    showOtherMonths: true,
    selectOtherMonths: true,
    showAnim : "slide",
    dayNamesMin: ['Di','Lu','Ma','Me','Je','Ve','Sa'],
});


$('#visiblite').click(function(){
    $('.closeye').toggleClass('hidden ');
    $('.openeye').toggleClass('hidden');
    $(this).toggleClass('btn-success').toggleClass('btn-danger');
})



$('.navigation').click(function(e){
    e.preventDefault()
    e.stopPropagation()
    const tab = $(this).data('id');
    $('.cart_rule_tab').hide();
    $('.tab-row.active').removeClass('active');
    $('#cart_rule_' + tab).show();
    $('#cart_rule_link_' + tab).parent().addClass('active');
});


downloadButton.on( "click", function(e) {
    e.preventDefault();
    showBulletin(downloadButton)
});

$('#prinyte').click(function(){
    downloadButton.trigger( "focus" );
    const classe = downloadButton.attr('rel');
    const campus = downloadButton.data('campus');
    $('#iframerecu').css({height:'700'});
    $('#iframerecu').attr('src', `/transcript/${campus}/${classe}/${periode}`);
    $('#myrecuprint').modal('toggle');
})


/**
 * Delete Student
 */
$('.delete-student').dblclick(function(e){
    e.preventDefault();
    const $this = $(this);
    const id = $(this).data("id");
    const html = $("<div id=\"dialog-confirm\" title=\"Êtes-vous sûr de vouloir supprimer  ? \">Attention si vous supprimez tous les données enregistrés dans cette catégorie seront supprimés définitivement. </div>")
    $("body").append(html)
    $("#dialog-confirm" ).dialog({
        resizable: true,
        height: "auto",
        width: 400,
        modal: true,
        buttons: {
            "OK": function() {
                $.ajax({
                    url:  `/student/${id}`,
                    type: 'DELETE',
                    data: window.csrf,
                    dataType:   'json',
                    success(){
                        notify('success', 'Supprimer !!!');
                        $this.parent().parent().remove()
                    },
                    error(error){
                        notify('error ',` ${error.statusText} !!! code: ${error.status}`)
                        console.error(error)
                    }
                });
                $( this ).dialog( "close" );
                html.remove()
            },
            Annuler: function() {
                $( this ).dialog( "close" );
                html.remove()
            }
        }
    });
});
