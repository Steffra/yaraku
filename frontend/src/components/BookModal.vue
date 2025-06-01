<script setup lang="ts">
import { ref, watch } from 'vue'
import { NButton, NInput, NForm, NFormItem, NCard, NModal } from 'naive-ui'
import { useBookActions } from '../composables/useBookActions'

const props = defineProps({
  show: Boolean,
  mode: {
    type: String,
    required: true,
  },
  book: {
    type: Object,
    default: null,
  },
})

const emit = defineEmits(['close'])

const { isSubmitting, createBook, editBook } = useBookActions()

const title = ref('')
const author = ref('')
const isModalVisible = ref(false)

const closeModal = () => {
  emit('close')
  title.value = ''
  author.value = ''
}

const handleSubmit = async () => {
  if (!title.value || !author.value) {
    alert('Both title and author are required.')
    return
  }

  try {
    if (props.mode === 'create') {
      await createBook(title.value, author.value)
    } else if (props.mode === 'edit' && props.book) {
      await editBook(props.book.id, title.value, author.value)
    }
    closeModal()
  } catch (error) {
    alert(`Failed to ${props.mode === 'create' ? 'add' : 'update'} book. Please try again.`)
  }
}

watch(
  () => props.show,
  (newValue) => {
    isModalVisible.value = newValue
    if (props.mode === 'edit' && props.book) {
      title.value = props.book.title || ''
      author.value = props.book.author || ''
    } else {
      title.value = ''
      author.value = ''
    }
  },
  { immediate: true }
)
watch(
  isModalVisible,
  (newValue) => {
    if (!newValue) {
      closeModal()
    }
  }
)
</script>

<template>
  <n-modal :preset="'dialog'" v-model:show="isModalVisible" @close="closeModal">
    <n-card :title="props.mode === 'create' ? 'Add New Book' : 'Edit Book'" :bordered="false">
      <n-form-item label="Title">
        <n-input v-model:value="title" placeholder="Enter book title" :disabled="props.mode === 'edit'" />
      </n-form-item>
      <n-form-item label="Author">
        <n-input v-model:value="author" placeholder="Enter book author" />
      </n-form-item>
      <div class="footer">
        <n-button secondary type="primary" :loading="isSubmitting" :disabled="isSubmitting" @click="handleSubmit">
          {{ props.mode === 'create' ? 'Submit' : 'Update' }}
        </n-button>
        <n-button type="tertiary" @click="closeModal">Cancel</n-button>
      </div>
    </n-card>
  </n-modal>
</template>

<style>
n-card {
  padding: 20px;
}
.footer {
  display: flex;
  justify-content: space-between;
  gap: 1rem;
}
.n-dialog__icon {
  display: none;
}
</style>