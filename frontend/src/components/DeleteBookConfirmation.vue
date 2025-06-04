<script setup lang="ts">
import { ref, watch } from 'vue'
import { NButton, NCard, NModal } from 'naive-ui'
import { useBookActions } from '../composables/useBookActions'

const props = defineProps({
  show: Boolean,
  book: {
    type: Object,
    default: null,
  },
})

const emit = defineEmits(['close'])

const { isSubmitting, deleteBook } = useBookActions()
const isModalVisible = ref(false)

const handleDelete = async () => {
  if (!props.book) return
  try {
    await deleteBook(props.book.id)
    emit('close')
  } catch (error) {
    alert('Failed to delete book. Please try again.')
  }
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
    <n-card title="Delete Book" :bordered="false">
      <p>Are you sure you want to delete the book "{{ props.book?.title }}"?</p>
      <div class="footer">
        <n-button type="default" @click="emit('close')">Cancel</n-button>
        <n-button type="error" :loading="isSubmitting" @click="handleDelete">Delete</n-button>
      </div>
    </n-card>
  </n-modal>
</template>

<style scoped>
.footer {
  display: flex;
  justify-content: space-between;
  gap: 1rem;
}
</style>