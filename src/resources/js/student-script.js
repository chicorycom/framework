const maDate = new Date().getMonth();

/**
 *
 * @param form
 */
function submitdrtrap(form){
    serialiseform(form)
        .then(res => {
            $("#showTraveau").modal('toggle');
            $('#deleted').remove()
        })
        .catch(e => {
            $('#deleted').removeAttr('id')
            $("#showTraveau").modal('toggle');
        })
}
$(document).ready(function (){
    $('.page-title strong').text(student.name)
    var $content = $('#contentRAS')
    var $privatnote = $('#privatnote')

    $('.see-grade-sheet').click(function (e){
        e.preventDefault()

        var period = $('#period').val();
        const $iframe = iframe(student.grade)
        const $content = $('#modal-content');
        $content.empty().html($iframe);
        $iframe.hide()
        $content.append('<div class="content-loader"><div class="loader"></div></div>');
        $iframe.load(function(){
            $(this).show();
            $content.find('.content-loader').remove()
        });
        $("#show-emploidu-temps").modal({backdrop: true});
        /*
        $("#dialog-choisperiod" ).dialog({
            resizable: false,
            height: "auto",
            width: 400,
            modal: true,
            buttons: {
                "OK": function() {
                    var period = $('#period').val();
                    const $iframe = iframe(`/grade/{{ $student->id }}/${period}`)
                    const $content = $('#modal-content');
                    $content.empty().html($iframe);
                    $iframe.hide()
                    $content.append('<div class="content-loader"><div class="loader"></div></div>');
                    $iframe.load(function(){
                        $(this).show();
                        $content.find('.content-loader').remove()
                    });
                    $("#show-emploidu-temps").modal({backdrop: true});
                    $( this ).dialog( "close" );
                },
                Annuler: function() {
                    $( this ).dialog( "close" );
                }
            }
        });
        */
    })

    $('#avatar-upload').submit(function(e){
        e.preventDefault();
        serialiseform(this).catch(error => {
            notify('error', error.responseText)
        });
    })

    $("#photo").fileinput({
        overwriteInitial: true,
        maxFileSize: 1500,
        showClose: false,
        showCaption: false,
        showBrowse: false,
        browseOnZoneClick: true,
        removeLabel: '',
        removeIcon: '<i class="glyphicon glyphicon-remove"></i>',
        removeTitle: 'Cancel or reset changes',
        elErrorContainer: '#kv-avatar-errors-2',
        msgErrorClass: 'alert alert-block alert-danger',
        defaultPreviewContent: `<img src="${student.avatar}" alt="Your Avatar" style="width:160px"><h6 class="text-muted">Cliquez pour sélectionner</h6>`,
        layoutTemplates: {main2: '{preview}  {remove} {browse}'},
        allowedFileExtensions: ["jpg", "png", "gif"]
    });

    const $dr = $('.devoir-ratrapage')
    if($dr.length){
        $dr.click(function (e) {
            e.preventDefault();
            const trdelet = $(this).parent().parent();
            trdelet.attr('id', 'deleted')
            var campus,classe,devoir,matiere,period,slug,titre,coesifient;   //table-responsive
            campus = $(this).data('campus');
            classe = $(this).data('classe');
            devoir = $(this).data('id');
            matiere = $(this).data('matiere');
            period = $(this).data('period');
            coesifient = $(this).data('coesifient');
            var typedevoir = $(this).data('type');
            var bareme = $(this).data('bareme');
            var datedevor = $(this).data('date');
            titre = "Devoir "+$(this).data('title')+" N° "+$(this).data('id') +" du "+$(this).data('date')+"<span class='pull-right'> Type :"+$(this).data('type')+"</span>";
            slug = devoir+'_'+matiere+'_'+period;
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
            notedevoir = `
                <p><strong>Baréme :</strong> 20</p></br>
                 <p><strong>Note moyenne :</strong> <span id='notemoy'>${$(this).data('moy')}</span></p>
                <p><strong>Note maximale :</strong> <span id='notemax'>${$(this).data('max')}</span></p>
                <p><strong>Note minimale :</strong> <span id='notemin'>${$(this).data('min')}</span></p>
                `;

            let content = `<form action='/devoir-ratrapage' id='AjouterDevoirsRatrapage' method='post' class='form-horzontal' onsubmit='submitdrtrap(this);return false;' role='form' >
                                    <table class='table table-hover  devoirs'>
                                    <thead><tr class='active'><th >Etudiants</th><th >Notes</th></thead>
                                        <tbody>
                                        <tr><td>{{ $student->prenom .' ' . $student->nom }} </td><td class="ratrapedevoirid" data-list-id="" ><input type="text" name="notes" class="form-control input-sm"> </td></tr> <tr><td>`;



            content += '<tr><td>' +
                '<input type="hidden" name="campus_id" value="'+campus+'">'+
                '<input type="hidden" name="classe" value="'+classe+'">'+
                '<input type="hidden" name="nbdevor" value="'+devoir+'">'+
                '<input type="hidden" name="matiere" value="'+matiere+'">'+
                '<input type="hidden" name="period" value="'+period+'">'+
                '<input type="hidden" name="type" value="'+typedevoir+'">'+
                '<input type="hidden" name="coesifient" value="'+coesifient+'">'+
                '<input type="hidden" name="bareme" value="'+bareme+'">'+
                '<input type="hidden" name="date" value="'+datedevor+'">'+
                '<input type="hidden" name="matricule" value="{{ $student->matricule }}">'+
                '</td><td  data-list-id="" >\
                        <button type="submit" name="ratrapd" class="btn btn-default pull-right">\
                        <i class="process-icon-save "></i>\
                       <span>Enregistrer</span>\
                       </button> \
               </td></tr>';

            content +="</tbody></table></form>";

            $('#contenerDevoirs').html(content);
            $('.titreDevoir').addClass('text-primary');
            $('#moyenMaxDevoirs').html(notedevoir);
            $('.titreDevoir').html(titre);
            $("#showTraveau").modal({backdrop: true});
        })
    }


    $("#sanction").click(function(e){
        e.preventDefault();
        var type = $(this).data('titre');
        var classe = $(this).data('classe')
        $('#typeSanction').val(type);
        $privatnote.removeClass().addClass(classe).text($(this).text())
        if($content.hasClass('disabled')){
            $content.removeClass('disabled')
        }
    });

    $("#retard").click(function(e){
        e.preventDefault();
        var type = $(this).data('titre');
        var classe = $(this).data('classe')
        $('#typeSanction').val(type);
        $privatnote.removeClass().addClass(classe).text($(this).text())
        if($content.hasClass('disabled')){
            $content.removeClass('disabled')
        }
    });
    $("#absence").click(function(e){
        e.preventDefault();
        var type = $(this).data('titre');
        var classe = $(this).data('classe')
        $('#typeSanction').val(type);
        $privatnote.removeClass().addClass(classe).text($(this).text())
        if($content.hasClass('disabled')){
            $content.removeClass('disabled')
        }

    });
    $("#retard-absence-sanction").submit(function(e){
        e.preventDefault();
        serialiseform(this)
            .then(data => {
                //$('#iframerecu').css({height:'300'});
                //$('#iframerecu').attr('src','billet-entree');
                // $("#myrecuprint").modal({backdrop: true});
            }).catch(errors => {
            notify('error  ', displayErrors(errors.responseJSON));
        })
    });

    $(".datepickernom").datetimepicker({
        regional:  "fr",
        showOtherMonths: true,
        selectOtherMonths: true,
        changeMonth: true,
        dayNamesMin: ['Di','Lu','Ma','Me','Je','Ve','Sa'],
    });

    $('#photo').change(function(){
        $('#avatar-save').fadeIn('slow')
    })

    $('.delete-retard-absence').click(function(e){
        const id = $(this).data('id');
        dialogueDelete(id, `/retard-absence-sanction/${id}`)
    })
})

