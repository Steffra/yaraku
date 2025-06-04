<script setup lang="ts">
import { ref, watch } from 'vue'
import { NButton, NCard, NModal, NRadioGroup, NRadio } from 'naive-ui'
import { useBookActions } from '../composables/useBookActions'

const props = defineProps({
  show: Boolean,
})

const emit = defineEmits(['close']) 

const { exportBooks } = useBookActions()

const exportOption = ref('titlesAndAuthors') 
const isModalVisible = ref(false)

const handleExport = async (format: string) => {
  const fields =
    exportOption.value === 'title'
      ? ['title']
      : exportOption.value === 'author'
      ? ['author']
      : ['title', 'author']

  await exportBooks(fields, format)
}

watch(
  () => props.show,
  (newValue) => {
    isModalVisible.value = newValue
  },
  { immediate: true }
)
watch(
  isModalVisible,
  (newValue) => {
    if (!newValue) {
      emit('close')
    }
  }
)
</script>

<template>
  <n-modal :preset="'dialog'" v-model:show="isModalVisible" @close="emit('close')">
    <n-card title="Export List" :bordered="false">
      <n-radio-group class="button-group" v-model:value="exportOption">
        <n-radio value="titlesAndAuthors" label="Titles and Authors"></n-radio>
        <n-radio  value="title" label="Titles"></n-radio>
        <n-radio value="author" label="Authors"></n-radio>
      </n-radio-group>
      <div class="footer">
        <n-button type="primary" @click="handleExport('csv')">⬇ Export as CSV</n-button>
        <span>or</span>
        <n-button type="primary" @click="handleExport('xml')">⬇ Export as XML</n-button>
      </div>  
    </n-card>
  </n-modal>
</template>

<style scoped>
.button-group {
  display: flex;
  flex-direction: column;
  justify-content: center;
}
.footer {
  display: flex;
  justify-content: center;
  gap: 0.5rem;
  margin-top:2rem;
  
  span {
    align-self: center;
    color: #666;
    font-size: 14px
  }
}

</style>