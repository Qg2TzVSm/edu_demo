### 公共部分

### 老师部分

### 学生部分

#### 1.获取个人资料：

##### URL:

- `/api/student/profile`

##### 请求方式：

- GET

##### 请求参数:

- 无
|参数名|是否必须|类型|说明|
|:---- |:---|:----- |----- |
|phone |是 |string | 手机号码 |
|name|是|string|用户名|

 

##### 返回示例:

```
{
  "status": "success",
  "code": 200,
  "result": {
    "id": 1,
    "avatar": null,
    "name": null,
    "email": "test1@test.com",
    "school": {
      "id": 3,
      "name": "test4444"
    }
  }
}
```

##### 返回参数说明:

|参数名|类型|说明|
|:----- |:-----|----- |


##### 备注:

- 更多返回错误代码请看首页的错误代码描述

#### 2.获取学校所有老师：

##### URL:

- `/api/student/teachers`

##### 请求方式：

- GET

##### 请求参数:

- 无

##### 返回示例:

```
{
  "status": "success",
  "code": 200,
  "result": [
    {
      "id": 1,
      "name": "test",
      "email": "test@test.com",
      "avatar": null,
      "followed_count": 1
    },
    {
      "id": 3,
      "name": "doge",
      "email": "dog@test.com",
      "avatar": null,
      "followed_count": 0
    }
  ]
}
```

#### 3.获取关注的所有老师：

##### URL:

- `/api/student/follows`

##### 请求方式：

- GET

##### 请求参数:

- 无

##### 返回示例:

```
{
  "status": "success",
  "code": 200,
  "result": [
    {
      "id": 1,
      "name": "test",
      "email": "test@test.com",
      "avatar": null
    }
  ]
}
```

#### 4.关注一个老师：

##### URL:

- `/api/student/teacher/{{$teacher}}/follow`

##### 请求方式：

- POST

##### 请求参数:

- `{{$teacher}}` 为老师的ID

##### 返回示例:

```
{
  "status": "success",
  "code": 200,
  "result": {
    "statusCode": 200,
    "message": "操作成功"
  }
}
```
#### 5.取消关注一个老师：

##### URL:

- `/api/student/teacher/{{$teacher}}/un-follow`

##### 请求方式：

- POST

##### 请求参数:

- `{{$teacher}}` 为老师的ID

##### 返回示例:

```
{
  "status": "success",
  "code": 200,
  "result": {
    "statusCode": 200,
    "message": "操作成功"
  }
}
```