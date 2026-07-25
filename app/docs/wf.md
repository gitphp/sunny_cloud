审批流程模块已按类型 / 模板 / 表单 / 节点 / 条件 / 申请 / 审批流水 / 抄送 生成。

### 配置侧
| 表 | 说明 |
|---|---|
| `wf_flow_type` | 流程类型 |
| `wf_flow_definition` | 流程模板（发布后可发起） |
| `wf_flow_form` | 自定义表单字段 |
| `wf_flow_node` | 审批节点 |
| `wf_flow_node_condition` | 条件分支（按表单字段跳转节点） |

### 运行侧
| 表 | 说明 |
|---|---|
| `wf_flow_apply` | 申请单 |
| `wf_flow_approve_record` | 同意/驳回/转审/加签/撤回流水 |
| `wf_flow_cc_user` | 抄送人及已读状态 |

### 主要接口
| 接口 | 说明 |
|---|---|
| `GET/POST/PUT... /wf/flow-types` | 流程类型 |
| `GET/POST/PUT... /wf/flow-definitions` | 模板（含 forms/nodes/conditions） |
| `GET /wf/applies/mine\|todo\|cc` | 我的申请 / 待办 / 抄送 |
| `POST /wf/applies` · `.../submit` · `.../withdraw` | 草稿 / 提交 / 撤回 |
| `POST /wf/applies/{id}/agree\|reject\|transfer\|add-sign` | 审批操作 |
| `POST /wf/cc/{id}/read` | 抄送标已读 |

### 前端
- `/backend/wf/todo` 待我审批
- `/backend/wf/applies` 我的申请（发起 / 编辑 / 详情）
- `/backend/wf/cc` 抄送我的
- `/backend/wf/flow-types` · `/backend/wf/flow-definitions`

### 种子
```bash
php artisan migrate
php artisan db:seed --class=WfSeeder
```
