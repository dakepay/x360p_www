webpackJsonp([27],{304:function(t,i,n){"use strict";Object.defineProperty(i,"__esModule",{value:!0});var o=n(418),e=n(588),s=n(16),a=s(o.a,e.a,!1,null,null,null);a.options.__file="src/views/business/clients/login-modal.vue",i.default=a.exports},418:function(t,i,n){"use strict";var o=n(5),e=n(0),s=(n.n(e),n(201),n(198)),a=n(200),l=n(199);i.a={mixins:[l.a,s.a,a.a],components:{},data:function(){return{info:{},url:""}},watch:{},mounted:function(){},computed:{},methods:{setInfo:function(t){var i=this;console.log(t),this.info=o.a.copy(t);var n={cid:this.info.cid,og_id:this.info.og_id};return this.info.uid&&(n.uid=this.info.uid),this.$rest("clients").add_url_param(this.info.cid,"domktoken").post(n).success(function(t){i.url=t.data.url}),this},cancel:function(){this.close(),this.info={}}}}},588:function(t,i,n){"use strict";var o=function(){var t=this,i=t.$createElement,n=t._self._c||i;return n("Modal",{directives:[{name:"drag-modal",rawName:"v-drag-modal"}],attrs:{"mask-closable":!1,title:t.modal$.title,width:"450"},on:{"on-cancel":t.cancel},model:{value:t.modal$.show,callback:function(i){t.$set(t.modal$,"show",i)},expression:"modal$.show"}},[n("div",{staticClass:"info"},[n("p",[t._v("登录地址如下:")]),t._v(" "),n("a",{attrs:{target:"_blank",href:t.url}},[t._v(t._s(t.url))]),t._v(" "),n("p",[t._v("点击上面的链接直接登录客户的系统")])]),t._v(" "),n("div",{attrs:{slot:"footer"},slot:"footer"},[n("Button",{attrs:{type:"primary",loading:t.saving},on:{click:t.cancel}},[t._v("关闭")])],1)])},e=[];o._withStripped=!0;var s={render:o,staticRenderFns:e};i.a=s}});