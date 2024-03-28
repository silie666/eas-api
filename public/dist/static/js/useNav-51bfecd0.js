import{aw as J,ax as L,ay as P,az as H,aA as O,aC as w,aD as F,aE as R,aF as Z,ab as S,q as h,cL as _,aa as ee,l as te,U as v,V as A,m as g,aq as ae,s as ne,n as N,Q as oe,u as se,cM as ue,E as le,cN as E,cO as re,cP as ie,a2 as de,cQ as ce}from"./index-0c4eef95.js";import{u as y}from"./hooks-e9aede39.js";const fe=function(t){return{deleteCommonApiLogin:async(e,n={})=>{const a="/common-api/login",o=new URL(a,w);let s;t&&(s=t.baseOptions);const l={method:"DELETE",...s,...n},r={},d={};e!=null&&(r.Authorization=String(e)),F(o,d);let f=s&&s.headers?s.headers:{};return l.headers={...r,...f,...n.headers},{url:R(o),options:l}},postCommonApiLogin:async(e,n={})=>{const a="/common-api/login",o=new URL(a,w);let s;t&&(s=t.baseOptions);const l={method:"POST",...s,...n},r={},d={};r["Content-Type"]="application/json",F(o,d);let f=s&&s.headers?s.headers:{};return l.headers={...r,...f,...n.headers},l.data=Z(e,l,t),{url:R(o),options:l}}}},V=function(t){const e=fe(t);return{async deleteCommonApiLogin(n,a){var r,d;const o=await e.deleteCommonApiLogin(n,a),s=(t==null?void 0:t.serverIndex)??0,l=(d=(r=L["AuthAuthApi.deleteCommonApiLogin"])==null?void 0:r[s])==null?void 0:d.url;return(f,m)=>P(o,O,H,t)(f,l||m)},async postCommonApiLogin(n,a){var r,d;const o=await e.postCommonApiLogin(n,a),s=(t==null?void 0:t.serverIndex)??0,l=(d=(r=L["AuthAuthApi.postCommonApiLogin"])==null?void 0:r[s])==null?void 0:d.url;return(f,m)=>P(o,O,H,t)(f,l||m)}}};class me extends J{deleteCommonApiLogin(e={},n){return V(this.configuration).deleteCommonApiLogin(e.authorization,n).then(a=>a(this.axios,this.basePath))}postCommonApiLogin(e={},n){return V(this.configuration).postCommonApiLogin(e.postCommonApiLoginRequest,n).then(a=>a(this.axios,this.basePath))}}function he(){const{$storage:t,$config:e}=S(),n=()=>{_().multiTagsCache&&(!t.tags||t.tags.length===0)&&(t.tags=ee),t.layout||(t.layout={layout:(e==null?void 0:e.Layout)??"vertical",theme:(e==null?void 0:e.Theme)??"default",darkMode:(e==null?void 0:e.DarkMode)??!1,sidebarStatus:(e==null?void 0:e.SidebarStatus)??!0,epThemeColor:(e==null?void 0:e.EpThemeColor)??"#409EFF"}),t.configure||(t.configure={grey:(e==null?void 0:e.Grey)??!1,weak:(e==null?void 0:e.Weak)??!1,hideTabs:(e==null?void 0:e.HideTabs)??!1,showLogo:(e==null?void 0:e.ShowLogo)??!0,showModel:(e==null?void 0:e.ShowModel)??"smart",multiTagsCache:(e==null?void 0:e.MultiTagsCache)??!1})},a=h(()=>t==null?void 0:t.layout.layout),o=h(()=>t.layout);return{layout:a,layoutTheme:o,initStorage:n}}const pe=te({id:"pure-app",state:()=>{var t,e;return{sidebar:{opened:((t=v().getItem(`${A()}layout`))==null?void 0:t.sidebarStatus)??g().SidebarStatus,withoutAnimation:!1,isClickCollapse:!1},layout:((e=v().getItem(`${A()}layout`))==null?void 0:e.layout)??g().Layout,device:ae()?"mobile":"desktop"}},getters:{getSidebarStatus(t){return t.sidebar.opened},getDevice(t){return t.device}},actions:{TOGGLE_SIDEBAR(t,e){const n=v().getItem(`${A()}layout`);t&&e?(this.sidebar.withoutAnimation=!0,this.sidebar.opened=!0,n.sidebarStatus=!0):!t&&e?(this.sidebar.withoutAnimation=!0,this.sidebar.opened=!1,n.sidebarStatus=!1):!t&&!e&&(this.sidebar.withoutAnimation=!1,this.sidebar.opened=!this.sidebar.opened,this.sidebar.isClickCollapse=!this.sidebar.opened,n.sidebarStatus=this.sidebar.opened),v().setItem(`${A()}layout`,n)},async toggleSideBar(t,e){await this.TOGGLE_SIDEBAR(t,e)},toggleDevice(t){this.device=t},setLayout(t){this.layout=t}}});function ge(){return pe(ne)}const B={outputDir:"",defaultScopeName:"",includeStyleWithColors:[],extract:!0,themeLinkTagId:"theme-link-tag",themeLinkTagInjectTo:"head",removeCssScopeName:!1,customThemeCssFileName:null,arbitraryMode:!1,defaultPrimaryColor:"",customThemeOutputPath:"/files/web/tecev-3.0-pc-client/node_modules/.pnpm/@pureadmin+theme@3.1.0/node_modules/@pureadmin/theme/setCustomTheme.js",styleTagId:"custom-theme-tagid",InjectDefaultStyleTagToHtml:!0,hueDiffControls:{low:0,high:0},multipleScopeVars:[{scopeName:"layout-theme-default",varsContent:`
        $subMenuActiveText: #fff !default;
        $menuBg: #001529 !default;
        $menuHover: #4091f7 !default;
        $subMenuBg: #0f0303 !default;
        $subMenuActiveBg: #4091f7 !default;
        $menuText: rgb(254 254 254 / 65%) !default;
        $sidebarLogo: #002140 !default;
        $menuTitleHover: #fff !default;
        $menuActiveBefore: #4091f7 !default;
      `},{scopeName:"layout-theme-light",varsContent:`
        $subMenuActiveText: #409eff !default;
        $menuBg: #fff !default;
        $menuHover: #e0ebf6 !default;
        $subMenuBg: #fff !default;
        $subMenuActiveBg: #e0ebf6 !default;
        $menuText: #7a80b4 !default;
        $sidebarLogo: #fff !default;
        $menuTitleHover: #000 !default;
        $menuActiveBefore: #4091f7 !default;
      `},{scopeName:"layout-theme-dusk",varsContent:`
        $subMenuActiveText: #fff !default;
        $menuBg: #2a0608 !default;
        $menuHover: #e13c39 !default;
        $subMenuBg: #000 !default;
        $subMenuActiveBg: #e13c39 !default;
        $menuText: rgb(254 254 254 / 65.1%) !default;
        $sidebarLogo: #42090c !default;
        $menuTitleHover: #fff !default;
        $menuActiveBefore: #e13c39 !default;
      `},{scopeName:"layout-theme-volcano",varsContent:`
        $subMenuActiveText: #fff !default;
        $menuBg: #2b0e05 !default;
        $menuHover: #e85f33 !default;
        $subMenuBg: #0f0603 !default;
        $subMenuActiveBg: #e85f33 !default;
        $menuText: rgb(254 254 254 / 65%) !default;
        $sidebarLogo: #441708 !default;
        $menuTitleHover: #fff !default;
        $menuActiveBefore: #e85f33 !default;
      `},{scopeName:"layout-theme-yellow",varsContent:`
        $subMenuActiveText: #d25f00 !default;
        $menuBg: #2b2503 !default;
        $menuHover: #f6da4d !default;
        $subMenuBg: #0f0603 !default;
        $subMenuActiveBg: #f6da4d !default;
        $menuText: rgb(254 254 254 / 65%) !default;
        $sidebarLogo: #443b05 !default;
        $menuTitleHover: #fff !default;
        $menuActiveBefore: #f6da4d !default;
      `},{scopeName:"layout-theme-mingQing",varsContent:`
        $subMenuActiveText: #fff !default;
        $menuBg: #032121 !default;
        $menuHover: #59bfc1 !default;
        $subMenuBg: #000 !default;
        $subMenuActiveBg: #59bfc1 !default;
        $menuText: #7a80b4 !default;
        $sidebarLogo: #053434 !default;
        $menuTitleHover: #fff !default;
        $menuActiveBefore: #59bfc1 !default;
      `},{scopeName:"layout-theme-auroraGreen",varsContent:`
        $subMenuActiveText: #fff !default;
        $menuBg: #0b1e15 !default;
        $menuHover: #60ac80 !default;
        $subMenuBg: #000 !default;
        $subMenuActiveBg: #60ac80 !default;
        $menuText: #7a80b4 !default;
        $sidebarLogo: #112f21 !default;
        $menuTitleHover: #fff !default;
        $menuActiveBefore: #60ac80 !default;
      `},{scopeName:"layout-theme-pink",varsContent:`
        $subMenuActiveText: #fff !default;
        $menuBg: #28081a !default;
        $menuHover: #d84493 !default;
        $subMenuBg: #000 !default;
        $subMenuActiveBg: #d84493 !default;
        $menuText: #7a80b4 !default;
        $sidebarLogo: #3f0d29 !default;
        $menuTitleHover: #fff !default;
        $menuActiveBefore: #d84493 !default;
      `},{scopeName:"layout-theme-saucePurple",varsContent:`
        $subMenuActiveText: #fff !default;
        $menuBg: #130824 !default;
        $menuHover: #693ac9 !default;
        $subMenuBg: #000 !default;
        $subMenuActiveBg: #693ac9 !default;
        $menuText: #7a80b4 !default;
        $sidebarLogo: #1f0c38 !default;
        $menuTitleHover: #fff !default;
        $menuActiveBefore: #693ac9 !default;
      `}]},be="/",ve="assets";function U(t){let e=t.replace("#","").match(/../g);for(let n=0;n<3;n++)e[n]=parseInt(e[n],16);return e}function $(t,e,n){let a=[t.toString(16),e.toString(16),n.toString(16)];for(let o=0;o<3;o++)a[o].length==1&&(a[o]=`0${a[o]}`);return`#${a.join("")}`}function Ae(t,e){let n=U(t);for(let a=0;a<3;a++)n[a]=Math.floor(n[a]*(1-e));return $(n[0],n[1],n[2])}function Te(t,e){let n=U(t);for(let a=0;a<3;a++)n[a]=Math.floor((255-n[a])*e+n[a]);return $(n[0],n[1],n[2])}function Q(t){return`(^${t}\\s+|\\s+${t}\\s+|\\s+${t}$|^${t}$)`}function j({scopeName:t,multipleScopeVars:e}){const n=Array.isArray(e)&&e.length?e:B.multipleScopeVars;let a=document.documentElement.className;new RegExp(Q(t)).test(a)||(n.forEach(o=>{a=a.replace(new RegExp(Q(o.scopeName),"g"),` ${t} `)}),document.documentElement.className=a.replace(/(^\s+|\s+$)/g,""))}function D({id:t,href:e}){const n=document.createElement("link");return n.rel="stylesheet",n.href=e,n.id=t,n}function Ce(t){const e={scopeName:"theme-default",customLinkHref:s=>s,...t},n=e.themeLinkTagId||B.themeLinkTagId;let a=document.getElementById(n);const o=e.customLinkHref(`${be.replace(/\/$/,"")}${`/${ve}/${e.scopeName}.css`.replace(/\/+(?=\/)/g,"")}`);if(a){a.id=`${n}_old`;const s=D({id:n,href:o});a.nextSibling?a.parentNode.insertBefore(s,a.nextSibling):a.parentNode.appendChild(s),s.onload=()=>{setTimeout(()=>{a.parentNode.removeChild(a),a=null},60),j(e)};return}a=D({id:n,href:o}),j(e),document[(e.themeLinkTagInjectTo||B.themeLinkTagInjectTo||"").replace("-prepend","")].appendChild(a)}function xe(){var m;const{layoutTheme:t,layout:e}=he(),n=N([{color:"#1b2a47",themeColor:"default"},{color:"#ffffff",themeColor:"light"},{color:"#f5222d",themeColor:"dusk"},{color:"#fa541c",themeColor:"volcano"},{color:"#fadb14",themeColor:"yellow"},{color:"#13c2c2",themeColor:"mingQing"},{color:"#52c41a",themeColor:"auroraGreen"},{color:"#eb2f96",themeColor:"pink"},{color:"#722ed1",themeColor:"saucePurple"}]),{$storage:a}=S(),o=N((m=a==null?void 0:a.layout)==null?void 0:m.darkMode),s=document.documentElement;function l(c=g().Theme??"default"){var i,p;if(t.value.theme=c,Ce({scopeName:`layout-theme-${c}`}),a.layout={layout:e.value,theme:c,darkMode:o.value,sidebarStatus:(i=a.layout)==null?void 0:i.sidebarStatus,epThemeColor:(p=a.layout)==null?void 0:p.epThemeColor},c==="default"||c==="light")d(g().EpThemeColor);else{const T=n.value.find(C=>C.themeColor===c);d(T.color)}}function r(c,i,p){document.documentElement.style.setProperty(`--el-color-primary-${c}-${i}`,o.value?Ae(p,i/10):Te(p,i/10))}const d=c=>{y().setEpThemeColor(c),document.documentElement.style.setProperty("--el-color-primary",c);for(let i=1;i<=2;i++)r("dark",i,c);for(let i=1;i<=9;i++)r("light",i,c)};function f(){y().epTheme==="light"&&o.value?l("default"):l(y().epTheme),o.value?document.documentElement.classList.add("dark"):document.documentElement.classList.remove("dark")}return{body:s,dataTheme:o,layoutTheme:t,themeColors:n,dataThemeChange:f,setEpThemeColor:d,setLayoutThemeColor:l}}function ke(t){return{all:t=t||new Map,on:function(e,n){var a=t.get(e);a?a.push(n):t.set(e,[n])},off:function(e,n){var a=t.get(e);a&&(n?a.splice(a.indexOf(n)>>>0,1):t.set(e,[]))},emit:function(e,n){var a=t.get(e);a&&a.slice().map(function(o){o(n)}),(a=t.get("*"))&&a.slice().map(function(o){o(e,n)})}}}const W=ke(),ye="data:image/jpeg;base64,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",Be="当前路由配置不正确，请检查配置";function Le(){var M;const t=oe(),e=ge(),n=se().options.routes,{wholeMenus:a}=ue(le()),o=((M=g())==null?void 0:M.TooltipEffect)??"light",s=h(()=>({width:"100%",display:"flex",alignItems:"center",justifyContent:"space-between",overflow:"hidden"})),l=h(()=>{var u;return(u=E())==null?void 0:u.username}),r=h(()=>l.value?{marginRight:"10px"}:""),d=h(()=>!e.getSidebarStatus),f=h(()=>e.getDevice),{$storage:m,$config:c}=S(),i=h(()=>{var u;return(u=m==null?void 0:m.layout)==null?void 0:u.layout}),p=h(()=>c.Title);function T(u){const b=g().Title;b?document.title=`${u.title} | ${b}`:document.title=u.title}async function C(){await new me().deleteCommonApiLogin({authorization:"Bearer "+re().accessToken}),E().logOut()}function z(){var u;ie.push((u=de())==null?void 0:u.path)}function I(){W.emit("openPanel")}function X(){e.toggleSideBar()}function Y(u){u==null||u.handleResize()}function K(u){var x;if(!u.children)return console.error(Be);const b=/^http(s?):\/\//,k=(x=u.children[0])==null?void 0:x.path;return b.test(k)?u.path+"/"+k:k}function q(u){a.value.length===0||G(u)||W.emit("changLayoutRoute",u)}function G(u){return ce.includes(u)}return{route:t,title:p,device:f,layout:i,logout:C,routers:n,$storage:m,backTopMenu:z,onPanel:I,getDivStyle:s,changeTitle:T,toggleSideBar:X,menuSelect:q,handleResize:Y,resolvePath:K,isCollapse:d,pureApp:e,username:l,userAvatar:ye,avatarsStyle:r,tooltipEffect:o}}export{xe as a,ge as b,he as c,W as e,Ce as t,Le as u};
