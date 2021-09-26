export default class Contextmenu extends HTMLElement {
    constructor() {
        super();
        const shadow = this.attachShadow({mode: 'open'});
        this.menu = document.createElement('ul')
        //this.menu.setAttribute('class','menu-bar');
        this.menu.innerHTML = this._html()

        const style = document.createElement('style');
        style.textContent = this._style()

        this.menu.querySelector('#logout')
            .addEventListener('click', this.logout)
        shadow.appendChild(style);
        shadow.appendChild(this.menu);
    }

    connectedCallback() {
       // console.log('Custom square element added to page.');
       if(this.hasAttribute('datas')){
           const datas = JSON.parse(this.getAttribute('datas'))
           const divided = document.createElement('hr')
           const hr = this.menu.querySelector('#view-if')
           Object.keys(datas).forEach((item, idx, data) => {
               const li = document.createElement('li')
               li.setAttribute('id', item)
                     li.innerHTML = this.template(datas,item)

                if(hr) this.menu.insertBefore(li, hr)

               const el = this.menu.querySelector(`#${item}`)
               el.addEventListener('click', ()=>this.actions(datas, item))
               if (idx === 0){
                 //  console.log("Last callback call at index " + idx + " with value " + item );
                   this.menu.insertBefore(divided, el)
               }

           })

       }

    }

    disconnectedCallback() {
        //console.log('Custom square element removed from page.');
        this.menu.innerHTML = ''
    }


    attributeChangedCallback(name, oldValue, newValue) {
        console.log('Custom square element attributes changed.');
        //updateStyle(this);
    }

    logout(e){
        e.preventDefault()
       const form = document.createElement("form");
       form.method = "POST";
       form.action = "/logout";
       Object.keys(window.csrf).forEach(data => {
           const element = document.createElement("input");
           element.value=window.csrf[data];
           element.name=data;
           form.appendChild(element);
       })
       document.body.appendChild(form);
       form.submit();
    }

    actions(datas, data){
        //console.log(datas[data])
        switch (data){
            case 'delete':
                return this.delete(datas['delete'])
            default:
                return this.showEdit(datas[data])
        }

    }
    translate(data){
        switch (data){
            case 'delete':
                return 'Supprimé'
            case 'edit':
                return 'Modifié'
            case 'show':
                return 'Voire'
            default:
                return data
        }
    }

    showEdit(url){
        window.location.href = url
    }
    delete(url){
        //console.log(url)
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
                        url,
                        type: 'DELETE',
                        data: window.csrf,
                        dataType:   'json',
                        success(){
                            notify('success', 'Supprimer !!!');
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
    }

    _style() {
        return `
             hr{
        margin: 5px 0px;
    }

   
     ul{
        padding: 10px 0px;
        margin: 0px;
        min-width: 10.3rem;
        list-style: none;

    }
     ul li{
        width: 100%;
        height: 100%;
        padding-bottom: 7px;
        padding-top: 7px;
        position: relative;
    }
     ul li a{
        text-decoration: none;
        color: rgb(34, 34, 34);
        display: flex;
        align-items: center;
        padding: 0px 61px 0px 14px;
        z-index: 2;
        position: relative;
    }
    ul li a svg{
        margin-right: 20px;
    }
     ul li:hover svg{
        fill: #fff;
    }
   ul li:hover a{
        color: #fff;
    }
     ul li:hover span{
        width: 90%;
    }
    span{
        width: 0rem;
        height: 100%;
        background: linear-gradient(90deg, #0095ff 36%, #8ccfff 75%);
        position: absolute;
        top: 0;
        left: 0;
        border-radius: 0px 25px 25px 0px;
        transition: 0.2s;
    }
    .text-main{
        text-align: center;
    }
        `
    }

    template(datas, data){
        return `<a href="#" data-${data}="${datas[data]} id='${data}'">
                    ${this.svgPath()[data]}
                    ${this.translate(data)}
                </a><span></span>`
    }


    _html(){
        return `
            <li>
                <a href="javascript:window.history.back()">
                   <svg enable-background="new 0 0 299.021 299.021" version="1.1" viewBox="0 0 299.02 299.02" xml:space="preserve" xmlns="http://www.w3.org/2000/svg" height="24px" width="24px">
                            <path d="m292.87 254.43c-2.288 0-4.443-1.285-5.5-3.399-0.354-0.684-28.541-52.949-146.17-54.727v51.977c0 2.342-1.333 4.48-3.432 5.513-2.096 1.033-4.594 0.793-6.461-0.63l-128.89-98.774c-1.519-1.165-2.417-2.967-2.417-4.876 0-1.919 0.898-3.72 2.417-4.888l128.89-98.77c1.87-1.426 4.365-1.667 6.461-0.639 2.099 1.026 3.432 3.173 3.432 5.509v54.776c3.111-0.198 7.164-0.37 11.947-0.37 43.861 0 145.87 13.952 145.87 143.14 0 2.858-1.964 5.344-4.75 5.993-0.469 0.121-0.931 0.169-1.405 0.169z"/>
                    </svg>
                    Retour
                </a>
                <span></span>
            </li>
            <li>
                <a href="javascript:window.location.reload()">
                    <svg enable-background="new 0 0 488.482 488.482" version="1.1" viewBox="0 0 488.48 488.48" xml:space="preserve" xmlns="http://www.w3.org/2000/svg" height="24px" width="24px">
                        <path d="m456.38 359.74 4.1-6.6c1.2-2.3 2.4-4.6 3.5-6.9 2.3-4.7 4.8-9.2 6.8-14 3.7-9.7 7.7-19.3 10.1-29.4 3.1-10 4.4-20.3 6.1-30.6 0.7-5.2 0.7-10.4 1.1-15.6l0.4-7.8v-2.8l-0.3-5.4-0.6-14.3-2.3-16.2c-0.4-2.7-0.7-5.5-1.3-8l-1.9-7.6c-1.4-5-2.4-10.2-4.2-15.1-6.4-19.9-16.1-38.7-27.7-56-12.1-17.1-26.6-32.3-42.7-45.6-15.7-13-33.1-24.5-52-33.1-7.5-3.2-15.3-6.1-23-8.8l-11.9-3.2c-4-1-7.9-2.3-12-2.8l-18.3-3-6.2-0.4-12.3-0.7-3.1-0.2h-2.6l-1.4 0.1-5.6 0.2-11.3 0.5c-1.8 0-4 0.4-6.1 0.7l-6.4 0.9-12.8 1.9-12 3c-4 1.1-8.1 1.8-11.9 3.4l-11.6 4.2-5.8 2.2-5.6 2.7-11.1 5.4c-58.8 30.5-101.2 89.3-112 153.5-0.6 4.6-1.1 9.1-1.5 13.4s-0.8 8.5-0.5 12.5c0.1 2.4 0.2 4.6 0.4 6.9-11.1-17.4-23.7-33.9-34-51.8-0.8-1.4-5.2-1.7-7.7-1-4.9 1.3-7.2 5.4-8.1 9.7-2.9 14.3 0.3 27.3 6.4 38.6 11.5 21.3 22.1 43.2 35.8 63.2l0.3 0.5c0.5 0.7 1 1.4 1.7 2.1 7 7.6 18.9 8.1 26.6 1 4.9-4.5 9.6-9.2 14.1-14.2 3.1-2.3 6.2-4.5 9.2-6.9 19.1-15 37.5-30.9 52.7-50.1 4.3-5.5 5.5-12.7-1-19.1-5.9-5.8-13.6-7.1-19.5-3.1-10.6 7.2-21.3 14.3-30.6 23-8.6 8-16.8 16.4-24.9 24.9 0.5-1.6 1-3.3 1.5-5.2 1.5-5.2 3-11.1 4.2-17.9 0.4-3.4 1.7-6.8 2.4-10.5 0.8-3.7 1.6-7.5 2.4-11.5 7-28.5 20.7-55.1 39.3-77.5 18.5-22.5 42.5-40 68.5-52.3 5.1-2.8 10.7-4.5 16.1-6.6 5.4-2.3 11-3.5 16.7-5.1 2.8-0.7 5.6-1.7 8.4-2.1l8.2-1.3 8.3-1.4 9.5-0.6c23.4-1.5 46.9 1.1 69.2 8.2 44.7 13.9 84.1 45.4 107.3 86.8l6 14.7c1.1 2.4 1.8 5 2.6 7.5l2.3 7.6c1.8 5 2.6 10.2 3.7 15.3l1.6 7.7c0.4 2.5 0.5 5 0.8 7.5l0.8 7.4c0.1 1.3 0.3 2.4 0.4 3.8l0.1 4.3 0.1 8.6 0.1 4.3v3-0.8l-0.1 1-1.3 15.6c-0.5 5.2-1.8 10.3-2.6 15.4-8.3 40.6-29.6 78.7-61.3 105.6-15.7 13.6-33.6 24.5-52.9 32.2-4.9 1.7-9.7 3.8-14.7 5.2l-15.1 3.9-14.6 2.3c-2.2 0.5-5.2 0.6-8.1 0.8l-8.6 0.5-4.3 0.2-1.7 0.1h-1.4l-7.8-0.2c-5.2-0.4-10.4-0.7-15.5-1.5-10.3-1.4-20.4-3.7-30.3-6.9-19.8-6.3-38.3-16.3-54.6-28.9-2.9-2.3-7.1-4.6-10.9-6.4-3.8-1.6-8.1-3.9-10.3-4.3-4.6-1-5.7 1-4.5 5 0.6 1.9 1.7 4.4 3.3 7.1 1.6 2.6 3.6 5.5 6.1 8.5 16.3 20 40.6 38 70.1 48.8 14.7 5.4 30.6 9.1 47.1 10.4l6.2 0.4 1.5 0.1 0.8 0.1h4.3c3.9-0.1 7.8-0.1 11.8-0.2l5.9-0.1 6.4-0.8 12.9-1.7c40.2-7.1 78.4-25.4 108.8-53.2 15.2-13.6 28.9-29.3 39.4-47.1z"/>
                    </svg>
                    Actualisé
                </a>
                <span></span>
            </li>
           
          <!--  <li>
                <a href="#">
                    <svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 0 24 24" width="24px"
                        fill="#000000">
                        <path d="M0 0h24v24H0z" fill="none" />
                        <path
                            d="M16 1H4c-1.1 0-2 .9-2 2v14h2V3h12V1zm-1 4l6 6v10c0 1.1-.9 2-2 2H7.99C6.89 23 6 22.1 6 21l.01-14c0-1.1.89-2 1.99-2h7zm-1 7h5.5L14 6.5V12z" />
                    </svg>
                    Copy
                </a>
                <span></span>
            </li>
            <li>
                <a href="#">
                    <svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 0 24 24" width="24px"
                        fill="#000000">
                        <path d="M0 0h24v24H0z" fill="none" />
                        <path
                            d="M19 2h-4.18C14.4.84 13.3 0 12 0c-1.3 0-2.4.84-2.82 2H5c-1.1 0-2 .9-2 2v16c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V4c0-1.1-.9-2-2-2zm-7 0c.55 0 1 .45 1 1s-.45 1-1 1-1-.45-1-1 .45-1 1-1zm7 18H5V4h2v3h10V4h2v16z" />
                    </svg>
                    Paste
                </a>
                <span></span>
            </li>-->
             <hr id="view-if">
            <li>
                <a href="/generals">
                     <svg height="24px" width="24px" enable-background="new 0 0 64 64" version="1.1" viewBox="0 0 64 64" xml:space="preserve" xmlns="http://www.w3.org/2000/svg"><path d="M2,40h6.559l1.153,3.46l-5.126,5.126c-0.781,0.781-0.781,2.047,0,2.828l8,8c0.78,0.781,2.048,0.781,2.828,0l5.126-5.126   L24,55.441V62c0,1.104,0.896,2,2,2h12c1.104,0,2-0.896,2-2v-6.559l3.46-1.153l5.126,5.126c0.78,0.781,2.048,0.781,2.828,0l8-8   c0.781-0.781,0.781-2.047,0-2.828l-5.126-5.126L55.441,40H62c1.104,0,2-0.896,2-2V26c0-1.104-0.896-2-2-2h-6.559l-1.153-3.46   l5.126-5.126c0.781-0.781,0.781-2.047,0-2.828l-8-8c-0.78-0.781-2.048-0.781-2.828,0L43.46,9.712L40,8.559V2c0-1.104-0.896-2-2-2   H26c-1.104,0-2,0.896-2,2v6.559l-3.46,1.153l-5.126-5.126c-0.78-0.781-2.048-0.781-2.828,0l-8,8c-0.781,0.781-0.781,2.047,0,2.828   l5.126,5.126L8.559,24H2c-1.104,0-2,0.896-2,2v12C0,39.104,0.896,40,2,40z M4,28h6c0.861,0,1.625-0.551,1.897-1.368l2-6   c0.239-0.719,0.052-1.511-0.483-2.046L8.829,14L14,8.829l4.586,4.585c0.535,0.536,1.328,0.722,2.046,0.483l6-2   C27.449,11.625,28,10.861,28,10V4h8v6c0,0.861,0.551,1.625,1.368,1.897l6,2c0.72,0.24,1.511,0.053,2.046-0.483L50,8.829L55.171,14   l-4.585,4.586c-0.536,0.536-0.723,1.328-0.483,2.046l2,6C52.375,27.449,53.139,28,54,28h6v8h-6c-0.861,0-1.625,0.551-1.897,1.368   l-2,6c-0.239,0.719-0.052,1.511,0.483,2.046L55.171,50L50,55.171l-4.586-4.585c-0.536-0.536-1.328-0.724-2.046-0.483l-6,2   C36.551,52.375,36,53.139,36,54v6h-8v-6c0-0.861-0.551-1.625-1.368-1.897l-6-2c-0.717-0.239-1.511-0.052-2.046,0.483L14,55.171   L8.829,50l4.585-4.586c0.536-0.536,0.723-1.328,0.483-2.046l-2-6C11.625,36.551,10.861,36,10,36H4V28z"/><path d="m32 40c4.411 0 8-3.589 8-8s-3.589-8-8-8-8 3.589-8 8 3.589 8 8 8zm0-12c2.206 0 4 1.794 4 4s-1.794 4-4 4-4-1.794-4-4 1.794-4 4-4z"/></svg>
                    Setting
                </a>
                <span></span>
            </li>
              <li>
                <a href="/logout" id="logout" data-logout="">
                    <svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 0 24 24" width="24px"
                        fill="#000000">
                        <path d="M0 0h24v24H0z" fill="none" />
                        <path
                            d="M19 6.41L17.59 5 12 10.59 6.41 5 5 6.41 10.59 12 5 17.59 6.41 19 12 13.41 17.59 19 19 17.59 13.41 12z" />
                    </svg>
                    Déconnexion
                </a>
                <span></span>
            </li>
    `;
    }

    toJson(att){

        let styles = att.split(';'), i = styles.length,
            json = {style: {}},
            style, k, v;
        styles.forEach(function (data){
            style = data.split(':');
            if(style[0].length > 0 &&  style[1].length > 0){
                k = style[0].trim();
                v = style[1].trim();
                json.style[k] = v;
            }
        })
        return json
    }

    svgPath(){
        return {
            show: '<svg height="24px" width="24px" enable-background="new 0 0 59.2 59.2" version="1.1" viewBox="0 0 59.2 59.2" xml:space="preserve" xmlns="http://www.w3.org/2000/svg"><path d="m51.062 21.561c-5.759-5.759-13.416-8.931-21.561-8.931s-15.801 3.171-21.56 8.931l-7.941 7.94 8.138 8.138c5.759 5.759 13.416 8.931 21.561 8.931s15.802-3.171 21.561-8.931l7.941-7.941-8.139-8.137zm-1.217 14.664c-5.381 5.381-12.536 8.345-20.146 8.345s-14.765-2.963-20.146-8.345l-6.724-6.724 6.527-6.527c5.381-5.381 12.536-8.345 20.146-8.345s14.765 2.963 20.146 8.345l6.724 6.724-6.527 6.527z"/><path d="m29.572 16.57c-7.168 0-13 5.832-13 13s5.832 13 13 13 13-5.832 13-13-5.831-13-13-13zm0 8c-2.757 0-5 2.243-5 5 0 0.552-0.448 1-1 1s-1-0.448-1-1c0-3.86 3.14-7 7-7 0.552 0 1 0.448 1 1s-0.447 1-1 1z"/></svg>',
            edit: '<svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 0 24 24" width="24px" fill="#000000"><path d="M3 17.25V21h3.75L17.81 9.94l-3.75-3.75L3 17.25zM20.71 7.04c.39-.39.39-1.02 0-1.41l-2.34-2.34c-.39-.39-1.02-.39-1.41 0l-1.83 1.83 3.75 3.75 1.83-1.83z" /></svg>',
            delete: '<svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 0 24 24" width="24px" fill="#000000"><path d="M6 19c0 1.1.9 2 2 2h8c1.1 0 2-.9 2-2V7H6v12zM19 4h-3.5l-1-1h-5l-1 1H5v2h14V4z" /></svg>'
        }
    }
}
