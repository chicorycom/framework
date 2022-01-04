/**
 * Created by assane on 03/08/16.
 */
$( function() {
    $("#myBtn").click(function(){
        $("#myModal").modal({backdrop: true});
    });
    $(document).ajaxStart(function () {
        $("#ajax_running").fadeIn();
        $("form button").attr("disabled", true);
        const modal_content = $('.modal-content')
        if(modal_content.length > 0){
            modal_content.LoadingOverlay("show")
        }
        panel_login_load_show()
    }).ajaxStop(function () {
        $("#ajax_running").fadeOut(1000);
        const modal_content = $('.modal-content')
        if(modal_content){
            modal_content.LoadingOverlay("hide")
        }
        $("form button").attr("disabled", false);
        panel_login_load_hide()
    });
    /*const modal_drag = $( ".modal-dialog" )
    if(modal_drag.length > 0){
        modal_drag.draggable();
    }*/

});

/**
 *
 * @param src
 * @returns {*|jQuery|HTMLElement}
 */
function iframe(src){
    return $('<iframe>', {
        src: src,
        id:  'myFrame',
        name: 'myFrame',
        frameborder: 0,
        scrolling: 'auto',
        width: '100%',
        height: '98%'
    })
}

/**
 *
 * @param name
 * @returns {*}
 */
function getE(name)
{
    if (document.getElementById)
        var elem = document.getElementById(name);
    else if (document.all)
        var elem = document.all[name];
    else if (document.layers)
        var elem = document.layers[name];
    return elem;
}

/**
 *
 * @param type
 * @param titre
 * @returns {*|NotyfyObject|NotyfyObject|NotyfyObject}
 */
function notify (type,titre){
    const $notify = notyfy({
        text: '<strong>'+type+' !</strong>'+titre,
        type: type,
        layout: 'topRight',
        theme: 'boolight',
        timeout: true,
        //closeWith: ['hover'],
        events: {}
    });

    setTimeout(()=>{
        $notify.close()
    }, 4000);

    return $notify;
}

function verification(){

    $.getJSON(
        './controller.php',{
            cat : 'verification',
        }, function(data){
            $("#loginid").val(data['login']);
            $("#passwordid").val(data['pass']);
        }
    );
}

/**
 *
 * @param {int} id
 * @param {string} url
 */
function delect(id, url) {
    $.ajax({
        url:  url,
        type: 'DELETE',
        data: {id, ...window.csrf},
        dataType:   'json',
        success(){
            notify('success',' Element supprimer!!!');
            window.setTimeout(function (){
                location.reload();
            }, 1000)

        },
        error(error){
            notify('error ',` ${error.statusText} !!! code: ${error.status}`)
            console.error(error)
        }
    });
}

function has_delete(id, url){
    return new Promise((resolve, reject) => {
        $.ajax({
            url:  url,
            type: 'DELETE',
            data: {id, ...window.csrf},
            dataType:   'json',
            success(data){
                resolve(data);
            },
            error(error){
                notify('error ',` ${error.statusText} !!! code: ${error.status}`)
                reject(error);
            }
        });
    })
}

function ajoutModifCyclClass(url,id,text) {
    $.post(
        'postUrl-'+url,{
            id_profile : id,
            classes : text
        }, function(data){
            if(data == 'Success'){
                notify('success  ',' Enregistrement effectuer !!!');
            }else{
                notify('error','Erreur de connexion!'+data);
            }
        },
        'html'
    );
}

function reloadePage(temp) {
    setTimeout(function(){
        window.location.reload(1);
    }, temp);
}

function gencode(size){
    getE('matricule').value = '';
    const chars = "123456789ABCDEFGHIJKLMNPQRSTUVWXYZ";
    for (let i = 1; i <= size; ++i)
        getE('matricule').value += chars.charAt(Math.floor(Math.random() * chars.length));
}

//preview IMG
function readURL(input) {
    if (input.files && input.files[0]) {
        var reader = new FileReader();
        reader.onload = function (e) {
            $("#previewR").attr('src', e.target.result);
        }
        reader.readAsDataURL(input.files[0]);
    }
}

/**
 *
 * @param {HTMLFormElement} form
 * @param reset
 * @returns {Promise<unknown>}
 */
function serialiseform (form, reset=true) {
  const $form = $(form);
  const formdata = (window.FormData) ? new FormData($form[0]) : null;
    Object.keys(window.csrf).forEach(function(key) {
        if(!formdata.has(key)){
            formdata.append(key, window.csrf[key])
        }
    });

  const data = (formdata !== null) ? formdata : $form.serialize();

  return new Promise((resolve, reject) => {
      $.ajax({
          url: $form.attr('action'),
          type: $form.attr('method'),
          contentType: false, // obligatoire pour de l'upload
          processData: false, // obligatoire pour de l'upload
          dataType: "json",
          data: data,
          success (response) {
              notify('success  ', " Enregistre !!!");
              if(reset) effacer(form)

              resolve(response);
          },
          error (err){
              //const error = JSON.parse(err.responseText)
              //console.log(error)
              reject(err)
          }
      });
  })
}


/**
 *
 * @param id
 */
function bs_input_file(id) {
    $(".input-file").before(
        function() {
            if ( ! $(this).prev().hasClass('input-ghost') ) {
                var element = $("<input type='file' class='input-ghost' style='visibility:hidden; height:0' id='"+id+"'>");
                element.attr("name",$(this).attr("name"));
                element.change(function(){
                    element.next(element).find('input').val((element.val()).split('\\').pop());
                });
                $(this).find("button.btn-choose").click(function(){
                    element.click();
                });
                $(this).find("button.btn-reset").click(function(){
                    element.val(null);
                    $(this).parents(".input-file").find('input').val('');
                });
                $(this).find('input').css("cursor","pointer");
                $(this).find('input').mousedown(function() {
                    $(this).parents('.input-file').prev().click();
                    return false;
                });
                return element;
            }
        }
    );
}

/**
 *
 * @param titre
 * @param content
 */
function rappel(titre,content){
    setTimeout(function () {
        $.gritter.add({
            title: titre,
            text: content,
            sticky: false,
            class_name: 'gritter-light',
            time: 8000
        });
        return false;
    }, 3000);
}

/**
 *
 * @param formulaire
 */
function effacer (formulaire) {
    $(':input', formulaire)
        .not(':button, :submit, :reset, :hidden, #periodeDevoir')
        .val('')
        .removeAttr('checked')
        .removeAttr('selected');
}

function editeLine () {
    if ($('[data-edit="stand-out"]').is("[contenteditable='true']")){
        $('[data-edit="stand-out"]').attr("contentEditable","false");
    }else{
        $('[data-edit="stand-out"]').attr("contentEditable","true");
    }
}

/**
 *
 * @param {int} id
 * @param {string} url
 */
function dialogueDelete(id,url) {
    $("body").append("<div id=\"dialog-confirm\" title=\"Êtes-vous sûr de vouloir supprimer  ? \">Attention si vous supprimez tous les données enregistrés dans cette catégorie seront supprimés définitivement. </div>")
    $("#dialog-confirm" ).dialog({
        resizable: true,
        height: "auto",
        width: 400,
        modal: true,
        buttons: {
            "OK": function() {
                delect(id,url);
                $( this ).dialog( "close" );
                $( this ).remove()
            },
            Annuler: function() {
                $( this ).dialog( "close" );
                $( this ).remove()
            }
        }
    });
}

/**
 *
 * @param campus
 * @param classe
 * @param slug
 */
function htmlContentTask(campus,classe,slug){
  const content =   $( "<div></div>", {
        "class": "row",
    })

    const contentTask = $( "<div></div>", {
        "class": "col-md-8 well"
    })

    const moyenMaxDevoirs = $( "<div></div>", {
        "class": "col-md-4 pull-right well"
    })


    contentTask.empty().appendTo(content);
    moyenMaxDevoirs.empty().appendTo(content)

    let contentable = `<table class='table table-hover  devoirs'><thead><tr class='active'><th >Etudiants</th><th >Notes</th><th ></th></thead><tbody>`;


    $.getJSON( `/devoir/${campus}/${classe}/${slug}`, function( itemData ) {
        itemData[0].forEach(item => {
            contentable += `<tr><td> ${item.students?.prenom}  ${item.students?.nom} </td><td class="edited" data-collum="notes" >${item.notes}</td> 
                                        <td class="text-right">
                                                <button class="btn btn-default edit-task-student"  data-id="${ item.id }">
                                                       <i class="icon-edit"></i> Modifier
                                                </button>
</td>
</tr>`;
        })

        contentable += "</tbody></table>";

        contentTask.empty().html(contentable);

        const notedevoir = `<p><strong>Baréme :</strong> 20</p></br>
            <p><strong>Note moyenne :</strong> <span >${itemData[1].moy}</span></p>
            <p><strong>Note maximale :</strong> <span >${itemData[1].max}</span></p>
            <p><strong>Note minimale :</strong> <span >${itemData[1].min}</span></p>
        `;

        moyenMaxDevoirs.empty().html(notedevoir);
        $( "#contenerTravail" ).empty().html(content);
        $("#showTraveau").modal({backdrop: true});

    }).then(()=>{

    }, (error)=>{notify('error', displayErrors(error.responseJSON))});
}


function date_heure(id) {
        var date = new Date;
        var  annee = date.getFullYear();
        var moi = date.getMonth();
        var mois = ['Janvier', 'Février', 'Mars', 'Avril', 'Mai', 'Juin', 'Juillet', 'Ao&ucirc;t', 'Septembre', 'Octobre', 'Novembre', 'D&eacute;cembre'];
        var  j = date.getDate();
        var jour = date.getDay();
        var jours = ['Dimanche', 'Lundi', 'Mardi', 'Mercredi', 'Jeudi', 'Vendredi', 'Samedi'];
        var h = date.getHours();
        if(h<10)
        {
            h = "0"+h;
        }
        var m = date.getMinutes();
        if(m<10)
        {
            m = "0"+m;
        }
        var s = date.getSeconds();
        if(s<10)
        {
            s = "0"+s;
        }
        var resultat = jours[jour]+' '+j+' '+mois[moi]+' '+annee+' il est '+h+':'+m+':'+s;
        document.getElementById(id).innerHTML = resultat;
        setTimeout('date_heure("'+id+'");','1000');
        return true;
}


function displayErrors(errors) {
    let str_errors = '<p><strong>' + (errors.length > 1 ? 'Errors' : 'Error') + '</strong></p><ol>';
    for (var error in errors) //IE6 bug fix
        if (error != 'indexOf') str_errors += '<li>' + errors[error] + '</li>';

        //$('#error').html(str_errors + '</ol>').removeClass('hide').fadeIn('slow');
    return str_errors + '</ol>'
}

function panel_login_load_show (){
    const front_login = $('#loginn')
    const front_reset = $('#front_reset')
    const back_reset = $('#back_reset')
    if(front_login.length > 0) front_login.LoadingOverlay("show")
    if(front_reset.length > 0) front_reset.LoadingOverlay("show")
    if(back_reset.length > 0) back_reset.LoadingOverlay("show")
}

function panel_login_load_hide (){
    const front_login = $('#loginn')
    const front_reset = $('#front_reset')
    const back_reset = $('#back_reset')
    if(front_login.length > 0) front_login.LoadingOverlay("hide")
    if(front_reset.length > 0) front_reset.LoadingOverlay("hide")
    if(back_reset.length > 0) back_reset.LoadingOverlay("hide")
}

function goBack() {
    window.history.back();
}

function invisible(){
    window.print();
}
