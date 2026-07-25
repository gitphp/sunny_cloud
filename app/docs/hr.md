人事组织模块已按 `hr_department` / `hr_post` / `hr_dept_leaders` / `hr_user_dept_post` 生成。

### 后端
- 迁移 / 模型 / 枚举 / 错误码（830 / 840 / 850）
- 部门树 CRUD + 负责人同步
- 岗位树 CRUD
- 员工任职分页 CRUD（唯一约束：同人同部门同岗位）

| 接口 | 说明 |
|---|---|
| `GET/POST/PUT/PATCH/DELETE /backend/api/hr/departments` | 部门树增改删 / 排序 / 状态 |
| `GET/PUT .../departments/{id}/leaders` | 查询 / 保存部门负责人 |
| `GET/POST/PUT/PATCH/DELETE /backend/api/hr/posts` | 岗位树增改删 / 排序 / 状态 |
| `GET/POST/PUT/DELETE /backend/api/hr/user-dept-posts` | 任职管理 |

### 前端
- 页面：`/backend/hr/departments` · `/backend/hr/posts` · `/backend/hr/user-dept-posts`
- 侧栏：人事管理 → 部门 / 岗位 / 任职

### 种子
`HrSeeder`：示例部门/岗位 + 菜单，并授权给 `super_admin` / `admin`。

```bash
php artisan migrate
php artisan db:seed --class=HrSeeder
```
