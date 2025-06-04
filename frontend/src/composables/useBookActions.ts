import { ref } from 'vue'

const BASE_URL = 'api/books'

export function useBookActions() {
  const isSubmitting = ref(false)
  const books = ref([])

  const fetchBooks = async (titleFilter = '', authorFilter = '', sortBy = '', sortOrder = 'asc') => {
    try {
      const queryParams = new URLSearchParams()
      if (titleFilter) queryParams.append('title', encodeURIComponent(titleFilter))
      if (authorFilter) queryParams.append('author', encodeURIComponent(authorFilter))
      if (sortBy) queryParams.append('sortBy', sortBy)
      queryParams.append('sortOrder', sortOrder)

      const response = await fetch(`${BASE_URL}?${queryParams.toString()}`)
      if (!response.ok) {
        throw new Error('Failed to fetch books')
      }
      books.value = await response.json()
    } catch (error) {
      console.error('Error fetching books:', error)
      throw error
    }
  }

  const createBook = async (title: string, author: string) => {
    isSubmitting.value = true
    try {
        const response = await fetch(BASE_URL, {
            method: 'POST',
        headers: {
          'Content-Type': 'application/json',
        },
        body: JSON.stringify({ title:encodeURIComponent(title), author:encodeURIComponent(author) }),
      })
      if (!response.ok) {
        throw new Error('Failed to create book')
      }
    } catch (error) {
      console.error('Error creating book:', error)
      throw error
    } finally {
      isSubmitting.value = false
    }
  }

  const editBook = async (id: string, title: string, author: string) => {
    isSubmitting.value = true
    try {
        const response = await fetch(`${BASE_URL}/${id}`, {
            method: 'PUT',
        headers: {
          'Content-Type': 'application/json',
        },
        body: JSON.stringify({ title:encodeURIComponent(title), author:encodeURIComponent(author) }),
      })
      if (!response.ok) {
        throw new Error('Failed to update book')
      }
    } catch (error) {
      console.error('Error updating book:', error)
      throw error
    } finally {
      isSubmitting.value = false
    }
  }

  const deleteBook = async (id: string) => {
    isSubmitting.value = true
    try {
        const response = await fetch(`${BASE_URL}/${id}`, {
            method: 'DELETE',
      })
      if (!response.ok) {
        throw new Error('Failed to delete book')
      }
    } catch (error) {
      console.error('Error deleting book:', error)
      throw error
    } finally {
      isSubmitting.value = false
    }
  }

  const exportBooks = async (fields: string[], format: string) => {
    try {
      const queryParams = new URLSearchParams()
      fields.forEach((field) => queryParams.append('fields[]', field))

      const response = await fetch(`${BASE_URL}/export/${format}?${queryParams.toString()}`)
      if (!response.ok) {
        throw new Error('Failed to export data')
      }

      const blob = await response.blob()
      const link = document.createElement('a')
      link.href = URL.createObjectURL(blob)
      link.download = `export.${format}`
      link.click()
    } catch (error) {
      console.error('Error exporting data:', error)
      alert('Failed to export data. Please try again.')
    }
  }

  return {
    isSubmitting,
    books,
    fetchBooks,
    createBook,
    editBook,
    deleteBook,
    exportBooks,
  }
}