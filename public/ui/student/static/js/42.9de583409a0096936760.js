webpackJsonp([42],{"4B3h":function(t,n,e){"use strict";function i(t){e("L8ty")}Object.defineProperty(n,"__esModule",{value:!0});var a=e("UXdF");e.n(a);for(var s in a)"default"!==s&&function(t){e.d(n,t,function(){return a[t]})}(s);var r=e("T5EO"),u=e.n(r),o=e("VMs6"),c=e("vSla"),d=i,f=c(u.a,o.a,!1,d,null,null);n.default=f.exports},K6Tt:function(t,n,e){n=t.exports=e("UTlt")(!0),n.push([t.i,"\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n","",{version:3,sources:[],names:[],mappings:"",file:"page.vue",sourceRoot:""}])},L8ty:function(t,n,e){var i=e("K6Tt");"string"==typeof i&&(i=[[t.i,i,""]]),i.locals&&(t.exports=i.locals);e("FIqI")("9d7a3956",i,!0,{})},T5EO:function(t,n,e){"use strict";function i(t){return t&&t.__esModule?t:{default:t}}Object.defineProperty(n,"__esModule",{value:!0});var a=e("CKVb"),s=i(a),r=e("gpPJ"),u=i(r);n.default={components:{Cell:u.default,Group:s.default},data:function(){return{data:{},isCate:!1,cateList:[]}},mounted:function(){this.init_data()},methods:{routerLink:function(t){this.$router.push({path:"./page/"+t})},init_data:function(){var t=this;this.$rest(this.data_uri).get().success(function(n){t.data=n.list,t.data[0].is_cate&&(t.isCate=!0,t.init_cate(t.data.page_id))}).error(function(n){t.toast(n.body.message||"获取数据失败~","error")})},init_cate:function(t){var n=this;t=t||1;var e="pages?parent_pid="+t+"&with_count=children_num";this.$rest(e).get().success(function(t){n.cateList=t.list}).error(function(t){n.toast(t.body.message||"获取数据失败~","error")})}},computed:{data_uri:function(){return"pages?page_id="+(this.$route.params.id||1)}}}},UXdF:function(t,n,e){"use strict";function i(t){return t&&t.__esModule?t:{default:t}}Object.defineProperty(n,"__esModule",{value:!0});var a=e("CKVb"),s=i(a),r=e("gpPJ"),u=i(r);n.default={components:{Cell:u.default,Group:s.default},data:function(){return{data:{},isCate:!1,cateList:[]}},mounted:function(){this.init_data()},methods:{routerLink:function(t){this.$router.push({path:"./page/"+t})},init_data:function(){var t=this;this.$rest(this.data_uri).get().success(function(n){t.data=n.list,t.data[0].is_cate&&(t.isCate=!0,t.init_cate(t.data.page_id))}).error(function(n){t.toast(n.body.message||"获取数据失败~","error")})},init_cate:function(t){var n=this;t=t||1;var e="pages?parent_pid="+t+"&with_count=children_num";this.$rest(e).get().success(function(t){n.cateList=t.list}).error(function(t){n.toast(t.body.message||"获取数据失败~","error")})}},computed:{data_uri:function(){return"pages?page_id="+(this.$route.params.id||1)}}}},VMs6:function(t,n,e){"use strict";var i=function(){var t=this,n=t.$createElement,e=t._self._c||n;return e("div",[t.isCate?[e("group",t._l(t.cateList,function(n){return e("cell",{attrs:{title:n.title,"is-link":""},nativeOn:{click:function(e){t.routerLink(n.page_id)}}})}))]:[e("div",{staticClass:"content",domProps:{innerHTML:t._s(t.data.content)}})]],2)},a=[],s={render:i,staticRenderFns:a};n.a=s}});
//# sourceMappingURL=42.9de583409a0096936760.js.map