<?php

$config = array (	
		//应用ID,您的APPID。
		'app_id' => "2018062760492108",

		//商户私钥，您的原始格式RSA私钥
		'merchant_private_key' => "MIIEpAIBAAKCAQEAmGdIj06o5G6WMVM3ScnlWLTWYb9MwPlkeEEjSHB62H+p5WLhqwtQopvZRl53EDELWv15DudY8T1rkQXs9aiXgve1BsP8Bx8fCjUh+LxSWzpmesf1URTy0YLuuy/ZAgeFRzmjBZ+Nwf9RaFd50j/Hr048W+JGc3AtORQ3H5fkYpLUHdRwd83+beVoa2gVCiTsIAIM6TW2InMG1t0Fj290IDUm5gaWPZZzoHwVx1+8BIN/K8TanyqGJew+R52nvsi30BzIHevABLvnmqj8d9//xJ6YBROiLtjz/LDqcgwolAvEcuFwEDykg0cZortk9tq9xxIwSWJ9o/5fy5WogQoAVwIDAQABAoIBAQCP3BbTJ7YngvgFcX7kCU+Tz4f6VuC12/Rpy1rp2zsquD+AhzlsAs9H34NWeH89an5EkrW+SsVbWtX53DUUouL6PqbDzyZM9qSPNx5tNB8tfFAWIEBCHFslR8ngKkdXMhAt7osjGLxwXnjS3Jfizk/I/DnfphVIjKAznBB4oyFzpZ7cxQNjKkGQzZjY+shntbxYCMbAdmQ5oAbbfiOZZgXHTQM/wy4wGaoSvc6bAMKmGruEx7lPCayAVaOk2OUpitYD9twNNSkrUNSmqcigyzrqgKPisiEgLJAU0J0yUUUnZEyPpSODZXVJXdxqJV5Lte8NC+K6QZZXr2xxN7Y0h9JRAoGBAMj51MHfz9BSccD+OwcV/ua87MvI8+OukHbIuAuRhGBR7B5jJ6I1Q5HN/i91UkOAQuoTd9hKTGZ63oJxFGSrh5XxelD05mQgkC/en5QFtwgexv+Fz1PDKPnnJsbbDl8DUxgULw13LzrpnePb8NN6Y9Sg4AdCQggg5dLFIzjo5lxpAoGBAMIhELfd2/RRPkXiFLyra2OaEyL0HVZKb51WLow5efnkJew0/zb1RKAZGQ0HoVEb/DaIBbU8/62NxXXou9BeBvJ+FU3Ook5WOAqVORY8CqLABrg6Lx2PWOzXJTvSwMJ9jS1ZTDigUsLpPMyNCz8FMWpKEv4Rn8FQZ3pQV3+vyN6/AoGAMnha750PCRfLLVYq6KqjarqDFOIQLVtHOC1L2sveXHn2O+NWrquFnnYGoVBrKjxpkXL2I8D00r+EZWmUX3ub/xG3T+FQglCTJRJZLMkKn+Vqv/yQhk56Wdesqz+TqlShZ2iaMF1/5OGKHxC6t8EClQEZgXkoL/ZjAL01DTOfthECgYEAhROhoEmIRL2E0OcgjA1+unKed7GcgtDYHqU7l4i3IyTRElFqOsR73LwXviTi4vUqOj+YmhtMsi6jlCaMyVQLsyPXetUR0l2sYSTWU3WpXNQCRzwrnDnuHb2GmrHozeq/Yrz6UT8mPNMiiJ6PfQ4UR+ariluOhjiWFvUIZlGcg4UCgYAqfWAP/mwp8y5MqArYnI1FZ7LKOwFRKrn4HtlEjuPTmiotA44976PM5eCYDh3iYqYZpG9A56HgDjGz5WJ96YNvo1BSFZLlo+jfk83lkro9GWPUID8odZzMZ2jqquXZPIz0T6mgUBE8XE0LynvFJUZMvUV/6Ld2lyOjmSxbA4XoXQ==",
		
		//异步通知地址
		'notify_url' => "http://hotel.doufenger.com/Alipay/notify_url",
		
		//同步跳转
		'return_url' => "http://hotel.doufenger.com/Alipay/return_url",

		//编码格式
		'charset' => "UTF-8",

		//签名方式
		'sign_type'=>"RSA2",

		//支付宝网关
		'gatewayUrl' => "https://openapi.alipay.com/gateway.do",

		//支付宝公钥,查看地址：https://openhome.alipay.com/platform/keyManage.htm 对应APPID下的支付宝公钥。
		'alipay_public_key' => "MIIBIjANBgkqhkiG9w0BAQEFAAOCAQ8AMIIBCgKCAQEAmGdIj06o5G6WMVM3ScnlWLTWYb9MwPlkeEEjSHB62H+p5WLhqwtQopvZRl53EDELWv15DudY8T1rkQXs9aiXgve1BsP8Bx8fCjUh+LxSWzpmesf1URTy0YLuuy/ZAgeFRzmjBZ+Nwf9RaFd50j/Hr048W+JGc3AtORQ3H5fkYpLUHdRwd83+beVoa2gVCiTsIAIM6TW2InMG1t0Fj290IDUm5gaWPZZzoHwVx1+8BIN/K8TanyqGJew+R52nvsi30BzIHevABLvnmqj8d9//xJ6YBROiLtjz/LDqcgwolAvEcuFwEDykg0cZortk9tq9xxIwSWJ9o/5fy5WogQoAVwIDAQAB",	
);