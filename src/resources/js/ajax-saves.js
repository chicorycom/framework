/**
 * Created by assane on 03/08/16.
 */
$(document).ready(function(){
    $.getScript('/js/alertValidatForme.js', (data)=>console.log);
    /*
    Ajout Modifie Supprime Employers
     */
    $("#employeeFormPersonnage").validate({
        rules: {
            "firstname": {
                "required": true,
                "minlength": 2,
                "maxlength": 41
            },
            "lastname": {
                "required": true,
                "minlength": 2,
                "maxlength": 41
            },
            "emailPersonnage": {
                "email": true,
                "minlength": 7,
                "maxlength": 255
            },
            "passwd": {
                "required": true,
                "rangelength": [8, 21]
            },
            "adressePersonage": {
                "required": true,
                "minlength": 7,
                "maxlength": 255
            },
            "telephonePersonnage": {
                "required": true,
                "regex": /^([0-9]+)$/
            }
        },
            submitHandler: function(form) {
                serialiseform (form)
                    .then(() => {
                        window.location.href="/employes";
                    })
                    .catch(e => {
                        console.log(e)
                        notify('error', displayErrors(e.responseJSON))
                    })
        },
        // override jquery validate plugin defaults for bootstrap 3
        highlight: function(element) {
            $(element).closest('.form-group').addClass('has-error');
        },
        unhighlight: function(element) {
            $(element).closest('.form-group').removeClass('has-error');
        },
        errorElement: 'span',
        errorClass: 'help-block',
        errorPlacement: function(error, element) {
            if(element.parent('.input-group').length) {
                error.insertAfter(element.parent());
            } else {
                error.insertAfter(element);
            }
        }
    });



    $(".personnelEmployer").click(function(e) {
        e.preventDefault();
        const id = $(this).data("id");
        const url = $(this).attr('href')
        dialogueDelete(id, url)
    });


    /**
     * Update Devoir for ID
     */
    var old = null;
    $(document).on('focus','.modifierdevoirid', function (event) {
        old = parseFloat(event.target.defaultValue) || parseFloat($(this).text());
    });

    $(document).on('click', '.edit-task-student', function (e){
        const $tr = $(this).parent().parent();

        $tr.find('.edited').attr('contentEditable', true)
        $tr.find('.edited').first().focus()

        $(this).html(`<i class="icon-save"></i> Enregistrer`)

        $(this).removeClass('edit-task-student')
        $(this).addClass('save-edit-task')

        old = parseFloat($tr.find('[data-collum=notes]').text().trim())
    })

    $(document).on('click', '.save-edit-task' , function () {
        const id = $(this).data("id");
        const $tr = $(this).parent().parent();
        const note = parseFloat($tr.find('[data-collum=notes]').text().trim())

        if(old !== note){
            $.ajax({
                url:  `/devoir/${id}/update`,
                type: 'PUT',
                data: {
                    ...window.csrf,
                    id : id,
                    value : note,
                },
                success() {
                    notify('success  ',' Enregistrement effectuer !!!');
                    old = note
                },
                error(error){
                    notify('error  ',' Error d\'enregistrement !!!');
                    console.error(error)
                },
            });
            $(this).addClass('edit-task-student')
            $(this).removeClass('save-edit-task')
            $(this).html(`<i class="icon-edit"></i> Modifier`)
            $tr.find('.edited').attr('contentEditable', false)
            $(this).blur()

        }else{
            $(this).addClass('edit-task-student')
            $(this).removeClass('save-edit-task')
            $(this).html(`<i class="icon-edit"></i> Modifier`)
            $tr.find('.edited').attr('contentEditable', false)
            $(this).blur()
        }
    });

    $(document).on('blur','.modifierdevoirid', function () {
        const id = $(this).data("list-id");
        const note = parseFloat($(this).text()) || parseFloat($(this).val());
        if(old !== note){
            $.ajax({
                url:  `/devoir/${id}/update`,
                type: 'PUT',
                data: {
                    ...window.csrf,
                    id : id,
                    value : note,
                },
                success(result) {
                    notify('success  ',' Enregistrement effectuer !!!');
                    old = note
                },
                error(error){
                    notify('error  ',' Error d\'enregistrement !!!');
                    console.error(error)
                }
            });
        }
    });

    $('#student-update').submit(function(e){
        e.preventDefault()
        serialiseform($(this), false)
            .then(()=>{
                goBack()
            })
            .catch(error => {
            notify('error', displayErrors(error.responseJSON))
        })
    })

    //logo insertion
    $("#logoInsertion").submit(function(e){
        e.preventDefault();
        const $form = $(this);

        serialiseform($form)
            .catch(error=>{
                notify('error ',` ${error.statusText} !!! code: ${error.status}`)
                console.error(error)
            })
    });

    $('.btn-print').click(function(){
        window.frames["myFrame"].focus();
        window.frames["myFrame"].print();
    })
});
