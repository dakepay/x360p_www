# 公共接口说明 

### 公用接口

主要为一些基础接口

开放的接口包括

* 1,/signin  登录
* 2,/captcha/login 登录验证码
* 3,/logout 退出

登录之后的token有效期为 7200 秒 

所有的API请求通过在header头 添加 [x-token] 字段 来作为验证身份的凭证


### 格式返回

```javascript
{
	"code":0,			//返回码 0 为成功 ，其他为失败
	"message":"",		//消息
	"data":{}  			//返回的数据
}
```

