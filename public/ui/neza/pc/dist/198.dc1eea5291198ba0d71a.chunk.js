webpackJsonp([198],{1226:function(t,a,s){"use strict";a.a={props:{config:{type:Object,default:function(){return{}}},value:{type:String,default:"1"}},data:function(){return{tabValue:this.value}},watch:{tabValue:function(t){this.$emit("input",t)}},methods:{dateChange:function(t,a){console.log(t,a)},nlbr:function(t){return t.replace(/[\n\r]+/g,"<br>")},handleSuccess:function(t,a){this.config.banner=t.data.file_url},handleSuccess1:function(t,a){this.config.logo=t.data.file_url},handleFormatError:function(t){var a=["image","jpg","jpeg","png","bmp"].join(",");this.$Notice.warning({title:"文件格式不正确",desc:"文件 "+t.name+" 格式不正确，请上传 "+a+" 格式的文件。"})},save:function(){this.handleSubmit()}},computed:{upload_header:function(){return{"x-token":this.$store.state.user.token,"x-file-key":"file"}},upload_post:function(){return{mod:"correct_upload"}}}}},1227:function(t,a,s){"use strict";a.a={props:{config:{type:Object,default:function(){return{}}},tab:{type:String,default:"1"}},data:function(){return{tabValue:this.value}},computed:{enableFieldsList:function(){return this.config.fields.filter(function(t){return t.enable})}}}},1358:function(t,a,s){"use strict";function e(t){n||s(1359)}var i=s(1226),l=s(1360),n=!1,c=s(11),o=e,r=c(i.a,l.a,!1,o,"data-v-5719aeb3",null);r.options.__file="src/views/system/configs/qrsign/setting.vue",a.a=r.exports},1359:function(t,a){},1360:function(t,a,s){"use strict";var e=function(){var t=this,a=t.$createElement,s=t._self._c||a;return s("Tabs",{model:{value:t.tabValue,callback:function(a){t.tabValue=a},expression:"tabValue"}},[s("TabPane",{attrs:{label:"扫码录入设置",name:"1"}},[s("div",{staticClass:"p-3 mb-3 b-b-1"},[s("img",{staticStyle:{width:"375px",height:"190px"},attrs:{src:t.config.banner}}),t._v(" "),s("div",{staticClass:"mt-2"},[s("Upload",{ref:"handupload",attrs:{action:"/api/upload",format:["image","jpg","jpeg","png","bmp"],"show-upload-list":!1,headers:t.upload_header,"on-format-error":t.handleFormatError,data:t.upload_post,"on-success":t.handleSuccess}},[s("Button",{attrs:{type:"primary",icon:"refresh"}},[t._v("更换")]),t._v(" "),s("label",{staticClass:"text-desc"},[t._v("（建议上传750*380大小的图片）")])],1)],1)]),t._v(" "),s("div",{staticClass:"p-3 mb-3 b-b-1"},[s("div",{staticClass:"mb-2"},[t._v("\n\t\t\t\t标题： "),s("Input",{staticStyle:{width:"375px"},attrs:{placeholder:"请输入大标题"},model:{value:t.config.title,callback:function(a){t.$set(t.config,"title",a)},expression:"config.title"}})],1),t._v(" "),s("div",{staticClass:"mb-2"},[t._v("\n\t\t\t\t描述： "),s("Input",{staticStyle:{width:"375px"},attrs:{type:"textarea",autosize:{minRows:3,maxRows:5},placeholder:"请输入描述"},model:{value:t.config.desc,callback:function(a){t.$set(t.config,"desc",a)},expression:"config.desc"}})],1)]),t._v(" "),s("div",{staticClass:"p-3 mb-3 b-b-1"},[s("table",{staticClass:"modal-table"},[s("thead",[s("th",[s("div",{staticClass:"ivu-table-cell"},[t._v("字段")])]),t._v(" "),s("th",[s("div",{staticClass:"ivu-table-cell"},[t._v("类型")])]),t._v(" "),s("th",[s("div",{staticClass:"ivu-table-cell"},[t._v("是否启用")])]),t._v(" "),s("th",[s("div",{staticClass:"ivu-table-cell"},[t._v("默认值")])])]),t._v(" "),t._l(t.config.must_fields,function(a){return s("tr",[s("td",[s("div",{staticClass:"ivu-table-cell"},[t._v(t._s(a.label))])]),t._v(" "),s("td",[s("div",{staticClass:"ivu-table-cell text-danger"},[t._v("必填")])]),t._v(" "),s("td",[s("div",{staticClass:"ivu-table-cell"},[s("i-switch",{model:{value:a.enable,callback:function(s){t.$set(a,"enable",s)},expression:"item.enable"}})],1)]),t._v(" "),s("td",[s("div",{staticClass:"ivu-table-cell"},[s("DatePicker",{staticStyle:{width:"200px"},attrs:{value:a.default_value,format:"yyyy-MM-dd",type:"date",placeholder:"Select date"},on:{"on-change":function(t){a.default_value=t}}})],1)])])}),t._v(" "),t._l(t.config.fields,function(a){return s("tr",[s("td",[s("div",{staticClass:"ivu-table-cell"},[t._v(t._s(a.label))])]),t._v(" "),s("td",[s("div",{staticClass:"ivu-table-cell"},[t._v("选填")])]),t._v(" "),s("td",[s("div",{staticClass:"ivu-table-cell"},[s("i-switch",{model:{value:a.enable,callback:function(s){t.$set(a,"enable",s)},expression:"item.enable"}})],1)]),t._v(" "),s("td",[s("div",{staticClass:"ivu-table-cell"},[s("Input",{attrs:{placeholder:"请输入字段默认值"},model:{value:a.default_value,callback:function(s){t.$set(a,"default_value",s)},expression:"item.default_value"}})],1)])])})],2)]),t._v(" "),s("div",{staticClass:"p-3"},[s("img",{staticStyle:{"max-width":"140px","max-height":"40px"},attrs:{src:t.config.logo}}),t._v(" "),s("div",{staticClass:"mt-2"},[s("Upload",{ref:"handupload",attrs:{action:"/api/upload",format:["image","jpg","jpeg","png","bmp"],"show-upload-list":!1,headers:t.upload_header,"on-format-error":t.handleFormatError,data:t.upload_post,"on-success":t.handleSuccess1}},[s("Button",{attrs:{type:"primary",icon:"refresh"}},[t._v("更换")]),t._v(" "),s("label",{staticClass:"text-desc"},[t._v("（建议上传140*40大小的图片）")])],1)],1)])]),t._v(" "),s("TabPane",{attrs:{label:"录入成功设置",name:"2"}},[s("div",{staticClass:"p-3 mb-3 b-b-1"},[s("div",{staticClass:"mb-2"},[t._v("\n\t\t\t\t标　　题： "),s("Input",{staticStyle:{width:"375px"},attrs:{placeholder:"请输入大标题"},model:{value:t.config.msg.title,callback:function(a){t.$set(t.config.msg,"title",a)},expression:"config.msg.title"}})],1),t._v(" "),s("div",{staticClass:"mb-2"},[t._v("\n\t\t\t\t描　　述： "),s("Input",{staticStyle:{width:"375px"},attrs:{type:"textarea",autosize:{minRows:3,maxRows:5},placeholder:"请输入描述"},model:{value:t.config.msg.description,callback:function(a){t.$set(t.config.msg,"description",a)},expression:"config.msg.description"}})],1),t._v(" "),s("div",{staticClass:"mb-2"},[t._v("\n\t\t\t\t跳转链接： "),s("Input",{staticStyle:{width:"375px"},attrs:{placeholder:"请输入跳转链接"},model:{value:t.config.msg.redirect_url,callback:function(a){t.$set(t.config.msg,"redirect_url",a)},expression:"config.msg.redirect_url"}})],1)])])],1)},i=[];e._withStripped=!0;var l={render:e,staticRenderFns:i};a.a=l},1361:function(t,a,s){"use strict";function e(t){n||s(1362)}var i=s(1227),l=s(1363),n=!1,c=s(11),o=e,r=c(i.a,l.a,!1,o,"data-v-116c082b",null);r.options.__file="src/views/system/configs/qrsign/preview.vue",a.a=r.exports},1362:function(t,a){},1363:function(t,a,s){"use strict";var e=function(){var t=this,a=t.$createElement,s=t._self._c||a;return s("Card",[s("p",{attrs:{slot:"title"},slot:"title"},[t._v("效果预览")]),t._v(" "),1==t.tab?s("div",{staticClass:"x-container"},[s("div",{staticClass:"bg-header",style:{backgroundImage:"url("+t.config.banner+")"}}),t._v(" "),s("div",{staticClass:"x-content"},[s("div",{staticClass:"x-form"},[s("div",{staticClass:"form-title"},[t._v("\n\t\t\t\t\t"+t._s(t.config.title)+"\n\t\t\t\t\t"),s("p",{staticClass:"desc"},[t._v("\n\t\t\t\t\t\t"+t._s(t.config.desc)+"\n\t\t\t\t\t")])]),t._v(" "),s("div",{staticClass:"form-body"},[s("div",{staticClass:"form-item"},[s("label",{staticClass:"form-item-label"},[t._v("姓　　名:")]),t._v(" "),s("div",{staticClass:"form-item-content"},[s("input",{staticClass:"x-input",attrs:{readonly:"",placeholder:"请输入姓名"}})])]),t._v(" "),s("div",{staticClass:"form-item"},[s("label",{staticClass:"form-item-label"},[t._v("性　　别:")]),t._v(" "),s("div",{staticClass:"form-item-content text-left"},[s("span",{staticClass:"x-radio checked"},[s("span",{staticClass:"x-radio-icon"}),t._v(" "),s("span",{staticClass:"x-radio-label"},[t._v("男")])]),t._v(" "),s("span",{staticClass:"x-radio"},[s("span",{staticClass:"x-radio-icon"}),t._v(" "),s("span",{staticClass:"x-radio-label"},[t._v("女")])])])]),t._v(" "),s("div",{staticClass:"form-item"},[s("label",{staticClass:"form-item-label"},[t._v("电话号码:")]),t._v(" "),s("div",{staticClass:"form-item-content"},[s("input",{staticClass:"x-input",attrs:{readonly:"",placeholder:"请输入电话号码"}})])]),t._v(" "),t._l(t.config.must_fields,function(a){return a.enable?s("div",{staticClass:"form-item text-left mt-1"},[s("label",{staticClass:"form-item-label"},[t._v(t._s(a.label)+":")]),t._v(" "),s("div",{staticClass:"form-item-content"},[s("input",{staticClass:"x-input",attrs:{readonly:"",placeholder:a.placeholder},domProps:{value:a.default_value}})])]):t._e()}),t._v(" "),t.enableFieldsList.length>0?s("div",{staticClass:"mt-3 mb-2"},[s("p",{staticClass:"x-divider"},[s("span",[t._v("以下选填")])])]):t._e(),t._v(" "),t._l(t.config.fields,function(a){return a.enable?s("div",{staticClass:"form-item text-left mt-1"},[s("label",{staticClass:"form-item-label"},[t._v(t._s(a.label)+":")]),t._v(" "),s("div",{staticClass:"form-item-content"},[s("input",{staticClass:"x-input",attrs:{readonly:"",placeholder:a.placeholder},domProps:{value:a.default_value}})])]):t._e()})],2)]),t._v(" "),s("Button",{staticClass:"x-btn-submit",attrs:{type:"primary",long:""}},[t._v("提交")]),t._v(" "),s("img",{staticClass:"logo",staticStyle:{"max-width":"140px","max-height":"40px"},attrs:{src:t.config.logo}})],1)]):s("div",{staticClass:"x-container"},[s("div",{staticClass:"x-msg-container"},[s("div",{staticClass:"icon-area"},[s("img",{attrs:{src:"/static/img/org/wxicon-success.png"}})]),t._v(" "),s("div",{staticClass:"text-area"},[s("h2",{staticClass:"title"},[t._v(t._s(t.config.msg.title))]),t._v(" "),s("p",{staticClass:"desc"},[t._v(t._s(t.config.msg.description))])]),t._v(" "),s("Button",{staticClass:"mt-3",attrs:{type:"primary",long:""}},[t._v("（5s后自动跳转） 确定")])],1)])])},i=[];e._withStripped=!0;var l={render:e,staticRenderFns:i};a.a=l},1544:function(t,a,s){"use strict";var e=s(1),i=s(211),l=s(209),n=s(1358),c=s(1361),o={bid:0,channel_name:"",qr_config:{}};a.a={mixins:[l.a,i.a],components:{QrsignSetting:n.a,QrsignPreview:c.a},data:function(){return{mc_id:0,tab:"1",info:e.b.copy(o)}},computed:{config:function(){return e.a.isEmpty(this.info.qr_config)&&(this.info.qr_config=e.b.copy(this.$store.state.gvars.configs.qrsign)),this.info.qr_config}},methods:{ok:function(){var t=this,a=e.b.copy(this.info);a.bid=this.bid$;var s="market_channels/"+a.mc_id;this.$rest(s).put(a).success(function(a){t.$Message.success("修改成功！"),t.$emit("on-success"),t.close()}).error(function(a){t.$Message.error(a.body.message||"添加失败~")})}}}},2079:function(t,a){},2080:function(t,a,s){"use strict";var e=function(){var t=this,a=t.$createElement,s=t._self._c||a;return s("Modal",{directives:[{name:"drag-modal",rawName:"v-drag-modal"}],attrs:{title:t.modal$.title,width:880,"mask-closable":!1},model:{value:t.modal$.show,callback:function(a){t.$set(t.modal$,"show",a)},expression:"modal$.show"}},[s("div",{staticClass:"row"},[s("div",{staticClass:"col-md-6 col-sm-12"},[s("qrsign-setting",{attrs:{config:t.config},model:{value:t.tab,callback:function(a){t.tab=a},expression:"tab"}})],1),t._v(" "),s("div",{staticClass:"col-md-6 col-sm-12"},[s("qrsign-preview",{attrs:{config:t.config,tab:t.tab}})],1)]),t._v(" "),s("div",{attrs:{slot:"footer"},slot:"footer"},[s("Button",{attrs:{type:"ghost"},on:{click:t.close}},[t._v("取消")]),t._v(" "),s("Button",{attrs:{type:"primary"},on:{click:t.ok}},[t._v("确定")])],1)])},i=[];e._withStripped=!0;var l={render:e,staticRenderFns:i};a.a=l},375:function(t,a,s){"use strict";function e(t){n||s(2079)}Object.defineProperty(a,"__esModule",{value:!0});var i=s(1544),l=s(2080),n=!1,c=s(11),o=e,r=c(i.a,l.a,!1,o,null,null);r.options.__file="src/views/recruiting/market/qr-setting-modal.vue",a.default=r.exports}});