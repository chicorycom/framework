import Contextmenu from './modules/contextmenu-module'
customElements.define('context-menu', Contextmenu);

document.onclick = hideMenu;
document.oncontextmenu = rightClick;


let cm = null


function hideMenu() {
    if(cm) showContextMenu(false)
}

function rightClick(e) {
   // e.preventDefault();
    if(cm) cm.remove()

   cm = document.createElement('context-menu')

    cm.style.position = 'absolute'
    cm.style.backgroundColor = '#fff'
    cm.style.borderRadius = '13px'
    cm.style.overflow = 'hidden'
    cm.style.boxShadow = '0px 0px 20px #00000024'
    cm.style.transition = 'clip-path .2s ease-in-out'
    cm.style.clipPath = 'circle(0% at 0% 0%)'
    cm.style.zIndex = '111';
    const trs = e.target.closest("tr")
   if(trs !== null){

       const datas = trs.dataset.rightClick
       //console.log(trs.dataset)
       if(datas !== undefined){
           //console.log(JSON.parse(student))
           cm.setAttribute('datas', datas)
       }

   }
   // console.log(this.hasAttribute('right-click'))
  //  const el = document.getElementsByTagName('context-menu')
   // console.log(cm.offsetHeight)
    cm.style.top =
        e.y + cm.offsetHeight > window.innerHeight
            ? window.innerHeight - cm.offsetHeight + 'px'
            : e.y + 'px';
    cm.style.left =
        e.x + cm.offsetWidth > window.innerWidth
            ? window.innerWidth - cm.offsetWidth + 'px'
            : e.x + 'px';
    showContextMenu()
    //console.log()
    //document.body.contains(el)
}

function showContextMenu(show = true) {
    if(show){
        document.body.prepend(cm)
        cm.style.clipPath = "circle(75%)";
    }else{
        cm.style.clipPath = "circle(0% at 0% 0%)";
        cm.remove()
    }
}



