<script setup lang="ts">
import { ref } from 'vue'
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

const handleDelete = async () => {
  if (!props.book) return
  try {
    await deleteBook(props.book.id)
    emit('close')
  } catch (error) {
    alert('Failed to delete book. Please try again.')
  }
}
</script>

<template>
  <n-modal :preset="'dialog'" v-model:show="props.show" @close="emit('close')">
    <n-card title="Delete Book" :bordered="false">
      <p>Are you sure you want to delete the book "{{ props.book?.title }}"?</p>
      <div class="footer">
        <n-button type="error" :loading="isSubmitting" @click="handleDelete">Delete</n-button>
        <n-button type="default" @click="emit('close')">Cancel</n-button>
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