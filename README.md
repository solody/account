# Finance account

本模块提供简单的财务管理功能：

- 每个用户可以创建多个记账账户。
- 用户可以对账户资金申请提现。
- 提供多种提现网关。
- 用户可以设置多个提现方式。
- 可配置最大提现限额。
- 可对账户类型配置资金提现周期。（进账款在一定时间之后才能提现）

## 数据结构

### Content Entity

- Account 账户
- Ledger  账户记录
- Withdraw  提现单
- WithdrawMethod   提现方式
  
  指定了使用的转账网关 `Gateway` config entity，收款账号信息

### Config Entity

- Gateway  转账网关
  
  指定了网关插件 `TransferGateway`，和插件配置数据

### Config

- AccountType (bundle)
  - withdraw_period 提现周期
  - minimum_withdraw 单笔最小提现限额
  - maximum_withdraw 单笔最大提现限额
  
## 插件类型

- TransferGateway 转账网关 

## 服务

- FinanceManager 财务管理器
  
  - 创建账户
  - 创建账户记录
  
## 事件处理器

- 提现单作 `转账 transfer` 状态转换时，执行打款操作

## 界面

- 提现申请提交（创建提现单）
- 提现单管理（列表页）
- 提现单详情页 (view)

## 接口

- 创建提现单
