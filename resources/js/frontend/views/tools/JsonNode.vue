<template>
  <div class="json-node" :class="{ collapsed: isCollapsed && expandable }">
    <div class="node-line" @click="onLineClick">
      <button
        v-if="expandable"
        type="button"
        class="toggle"
        :aria-expanded="!isCollapsed"
        @click.stop="$emit('toggle', path)"
      >
        {{ isCollapsed ? '▶' : '▼' }}
      </button>
      <span v-else class="toggle-spacer" />

      <template v-if="keyName !== undefined && keyName !== null">
        <span class="key" @click.stop="$emit('select-path', path)">"{{ keyName }}"</span>
        <span class="colon">: </span>
      </template>

      <template v-if="type === 'object'">
        <span class="brace">{</span>
        <span v-if="isCollapsed" class="preview" @click.stop="$emit('toggle', path)">
          …{{ Object.keys(value).length }}
        </span>
        <span v-if="isCollapsed" class="brace">}</span>
      </template>

      <template v-else-if="type === 'array'">
        <span class="brace">[</span>
        <span v-if="isCollapsed" class="preview" @click.stop="$emit('toggle', path)">
          …{{ value.length }}
        </span>
        <span v-if="isCollapsed" class="brace">]</span>
      </template>

      <template v-else>
        <span
          v-if="!editing"
          class="leaf"
          :class="type"
          :title="'点击编辑 · ' + path"
          @dblclick.stop="startEdit"
          @click.stop="$emit('select-path', path)"
        >{{ displayValue }}</span>
        <input
          v-else
          ref="inputRef"
          v-model="editText"
          class="leaf-input"
          @blur="commitEdit"
          @keydown.enter.prevent="commitEdit"
          @keydown.esc.prevent="cancelEdit"
          @click.stop
        />
      </template>
    </div>

    <div v-if="expandable && !isCollapsed" class="children">
      <JsonNode
        v-for="(child, idx) in children"
        :key="child.key + '-' + idx"
        :value="child.value"
        :key-name="child.key"
        :path="child.path"
        :collapsed-paths="collapsedPaths"
        @toggle="$emit('toggle', $event)"
        @select-path="$emit('select-path', $event)"
        @update-value="$emit('update-value', $event)"
      />
      <div class="node-line close-line">
        <span class="toggle-spacer" />
        <span class="brace">{{ type === 'array' ? ']' : '}' }}</span>
      </div>
    </div>
  </div>
</template>

<script setup>
import { computed, nextTick, ref } from 'vue';

defineOptions({ name: 'JsonNode' });

const props = defineProps({
  value: { required: true },
  keyName: { default: undefined },
  path: { type: String, default: '$' },
  collapsedPaths: { type: Object, required: true },
});

const emit = defineEmits(['toggle', 'select-path', 'update-value']);

const editing = ref(false);
const editText = ref('');
const inputRef = ref(null);

const type = computed(() => {
  if (props.value === null) return 'null';
  if (Array.isArray(props.value)) return 'array';
  return typeof props.value;
});

const expandable = computed(() => type.value === 'object' || type.value === 'array');
const isCollapsed = computed(() => props.collapsedPaths.has(props.path));

const children = computed(() => {
  if (type.value === 'array') {
    return props.value.map((item, i) => ({
      key: String(i),
      value: item,
      path: `${props.path}[${i}]`,
    }));
  }
  if (type.value === 'object') {
    return Object.keys(props.value).map((k) => ({
      key: k,
      value: props.value[k],
      path: `${props.path}.${k}`,
    }));
  }
  return [];
});

const displayValue = computed(() => {
  if (type.value === 'string') return JSON.stringify(props.value);
  if (type.value === 'null') return 'null';
  return String(props.value);
});

function onLineClick() {
  emit('select-path', props.path);
}

function startEdit() {
  if (expandable.value) return;
  editing.value = true;
  editText.value = type.value === 'string' ? props.value : displayValue.value;
  nextTick(() => inputRef.value?.focus());
}

function cancelEdit() {
  editing.value = false;
}

function commitEdit() {
  if (!editing.value) return;
  editing.value = false;
  const raw = editText.value;
  let next;
  try {
    if (type.value === 'string') {
      next = raw;
    } else if (type.value === 'number') {
      const n = Number(raw);
      if (Number.isNaN(n)) return;
      next = n;
    } else if (type.value === 'boolean') {
      if (raw === 'true') next = true;
      else if (raw === 'false') next = false;
      else return;
    } else if (type.value === 'null') {
      if (raw === 'null') next = null;
      else return;
    } else {
      next = JSON.parse(raw);
    }
  } catch {
    return;
  }
  emit('update-value', { path: props.path, value: next });
}
</script>

<style scoped>
.json-node {
  font-family: Consolas, "Courier New", Monaco, monospace;
  font-size: 13px;
  line-height: 1.55;
}

.node-line {
  display: flex;
  align-items: flex-start;
  gap: 0;
  padding: 1px 4px;
  border-radius: 3px;
  cursor: default;
}

.node-line:hover {
  background: rgba(231, 76, 60, 0.06);
}

.toggle {
  width: 16px;
  flex: 0 0 16px;
  border: 0;
  background: transparent;
  color: #9ca3af;
  font-size: 10px;
  line-height: 1.55;
  padding: 0;
  cursor: pointer;
}

.toggle:hover {
  color: #e74c3c;
}

.toggle-spacer {
  width: 16px;
  flex: 0 0 16px;
  display: inline-block;
}

.key {
  color: #a855f7;
  cursor: pointer;
}

.colon {
  color: #6b7280;
}

.brace {
  color: #6b7280;
}

.preview {
  color: #9ca3af;
  margin: 0 2px;
  cursor: pointer;
}

.leaf {
  cursor: pointer;
  word-break: break-all;
}

.leaf.string {
  color: #16a34a;
}

.leaf.number {
  color: #2563eb;
}

.leaf.boolean {
  color: #ea580c;
}

.leaf.null {
  color: #9ca3af;
  font-style: italic;
}

.leaf-input {
  flex: 1;
  min-width: 80px;
  border: 1px solid #e74c3c;
  border-radius: 3px;
  padding: 0 6px;
  font: inherit;
  outline: none;
}

.children {
  margin-left: 16px;
  border-left: 1px dashed #e5e7eb;
  padding-left: 2px;
}

.close-line {
  color: #6b7280;
}
</style>
