/**
 *
 * @param url
 * @returns {Promise<DocumentFragment|HTMLTemplateElement|HTMLDivElement>}
 */
async function handleSectionTrigger (url) {

    let template = document.createElement('div');
    let response = null;
    const $content = $('#classroom')

    localStorage.setItem('activeSectionClass', url);

    //$content.addClass('classroom-loader')
    $content.html('<div class="classroom-loader"><div class="loader"></div></div>')
    try{
        response = await $.get(url);
    }catch (e){
        if(e.status === 404){
            response = e.responseText;
        }
    }

    /**
     *
     * @type {HTMLScriptElement}
     */
    template.innerHTML = response;

    /**
     *
     * @type {HTMLScriptElement}
     */
    const element = template.getElementsByTagName("script");
    const scripts = Array.prototype.slice.call(element)


    /**
     *
     * @type {HTMLTemplateElement}
     */
    const taskTemplate = template.getElementsByTagName('template')[0];

    template = taskTemplate ? taskTemplate : template;
    const clone = taskTemplate ? document.importNode(template.content, true) : template;

    if(scripts.length > 0 && template) {
        scripts.forEach(script => {
            clone.appendChild(script);
        })
    }

    $content
        .empty()
        .hide()
        .html(clone)
        .fadeIn(1000);
    //$content.removeClass('classroom-loader')

    return clone;
}


$(document).ready(function(){
    const link =  $('.link')
    link.click(function(e) {
        e.preventDefault()
        const url = $(this).attr('href').split('#')[1]
        const title = $(this).data('title')
        $('.page-title strong').text(title)
        link.removeClass('link-active')
        $(this).addClass('link-active')
        handleSectionTrigger(url).then()
        $('#emplois-du-temps-title').text(title)
    });

    const section = localStorage.getItem('activeSectionClass');



    if(section){
        const activeSection = section.split('/')[3]
        const history =  window.location.pathname.split('/')[2]
        console.log(activeSection, history)
        if(activeSection === history){
            handleSectionTrigger(section).then(()=>{
                const link = $(`a[href$='${section}']`)
                const title = link.data('title')
                link.addClass('link-active')
                $('.page-title strong').text(title)
                $('#emplois-du-temps-title').text(title)
            })
        }else{
            $('#classroom')
                .empty();
            localStorage.removeItem('activeSectionClass');
        }

    }

})


