<template>
    <div>
        <el-upload :show-file-list="false" :http-request="handleFileUpload" drag accept=".png, .jpg, .jpeg, .webp, .mp4, .gif, .webm">
            <font-awesome-icon icon="cloud-arrow-up" class="upload__icon"/>
            <div class="el-upload__text">
                Przeciągnij plik tutaj lub <em>kliknij, aby przesłać</em>
            </div>
            <template #tip>
                <div class="el-upload__tip">
                    Pliki jpg/png/gif/webp/mp4/webm o rozmiarze mniejszym niż 10 MB. Maksymalna ilość plików: 3
                </div>
            </template>
        </el-upload>
        <div class="upload__files" v-if="!isFileListLoading">
            <div class="files__item" v-for="(file, index) in fileList" :key="index" :class="{'files__item-active': file.name === props.advert.fileName}">
                <img v-if="file.type == 'img'" :src="file.url">
                <video v-else>
                    <source :src="`${file.url}#t=0.1`">
                    Twoja przeglądarka nie wspiera odtwarzacza wideo.
                </video>
                <div class="files__item-info">
                    <a :href="file.url" target="_blank">{{ file.name }}</a>
                </div>
                <div class="files__item-action">
                    <font-awesome-icon @click="handleFileHighlight(file)" icon="star" title="Ustaw jako aktywny plik reklamy"/>
                    <font-awesome-icon @click="handleFileRemove(file)" icon="trash" title="Usuń plik" />
                </div>
            </div>
        </div>
        <div class="upload__files-loading" v-else>
            <div>Trwa pobieranie plików reklamy...</div>
        </div>
    </div>
</template>

<script setup>
    import { ref, onMounted } from 'vue';

    import { useAdminStore } from '@/stores/AdminStore';
    import { useAdStore } from '@/stores/AdStore';
    import NotificationService from '@/services/notification.service';

    let store = useAdStore();

    const props = defineProps({
        advert: { type: Object, required: true, default: {} },
        mode: { type: String, required: false, default: 'user' }
    });

    const emit = defineEmits(['update']);

    const isFileListLoading = ref(false);
    const fileList = ref([]);

    onMounted(() => {
        if (props.mode === 'admin') store = useAdminStore();
        fetchFiles();
    });

    const fetchFiles = () => {
        isFileListLoading.value = true;
        store.fetchFiles(props.advert.id)
                .then((response) => {
                    fileList.value = response.data;
                })
                .finally(() => {
                    isFileListLoading.value = false;
                })
    }

    const handleFileUpload = (form) => {
        if (fileList.value.length >= 3) {
            NotificationService.displayMessage('error', 'Maksymalna liczba plików które można przesłać wynosi 3 pliki.');
            return;
        }

        const notify = NotificationService.displayMessage('info', 'Trwa przesyłanie pliku...', 0);
        const formData = new FormData();
        formData.append('file', form.file);

        store.uploadFile(props.advert.id, formData)
            .then((response) => {
                fileList.value.push(response.data.file);
                emit('update', response.data.fileName, response.data.fileType);
                NotificationService.displayMessage('success', 'Pomyślnie przesłano plik.');
            })
            .catch(() => {
                NotificationService.displayMessage('error', 'Wystąpił błąd przy przesyłaniu pliku, prosimy spróbować ponownie później.');
            })
            .finally(() => {
                notify.close();
            })
    };

    const handleFileRemove = async (file) => {
        if (props.advert.fileName == file.name) {
            const isConfirmed = await NotificationService.displayConfirmation('Czy na pewno chcesz usunąć plik będący plikiem aktywnym dla twojej reklamy?');
            if (!isConfirmed) return;
        }

        const notify = NotificationService.displayMessage('info', 'Trwa usuwanie pliku...', 0);
        store.deleteFile(props.advert.id, file.name)
            .then((response) => {
                NotificationService.displayMessage('success', 'Pomyślnie usunieto plik.');
                emit('update', response.data.fileName, response.data.fileType);
                const fileIndex = fileList.value.findIndex(item => item.name === file.name);
                if (fileIndex !== -1) {
                    fileList.value.splice(fileIndex, 1);
                }
            })
            .catch(() => {
                NotificationService.displayMessage('error', 'Wystąpił błąd przy usuwaniu pliku, prosimy spróbować ponownie później.');
            })
            .finally(() => {
                notify.close();
            })
    };

    const handleFileHighlight = (file) => {
        const notify = NotificationService.displayMessage('info', 'Trwa ustawianie aktywnego pliku reklamy...', 0);
        store.highlightFile(props.advert.id, file.name)
            .then((response) => {
                NotificationService.displayMessage('success', 'Pomyślnie ustawiono aktywny plik.');
                emit('update', response.data.fileName, response.data.fileType);
            })
            .catch(() => {
                NotificationService.displayMessage('error', 'Wystąpił błąd przy ustawianiu aktywnego pliku, prosimy spróbować ponownie później.');
            })
            .finally(() => {
                notify.close();
            })
    };
</script>

<style lang="scss" scoped>
    .upload__icon {
        font-size: 6rem;
        color: $--color-text-muted-2;
        margin-bottom: 16px;
    }

    .upload__files {
        display: flex;
        flex-direction: column;

        &-loading {
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 20px;
            color: $--color-text-muted-2;
        }
    }

    .files__item {
        display: flex;
        align-items: center;
        border: 1px solid $--color-text-muted-4;
        border-radius: 6px;
        margin-top: 15px;
        padding: 10px;

        &-active {
            border: 1px solid $--color-primary;
        }

        img {
            width: 75px;
            height: 100%;
            object-fit: contain;
        }

        video {
            width: 75px;
            height: 100%;
        }

        &-info {
            margin-left: 10px;

            a {
                color: inherit;
                text-decoration: none;
                transition: color .25s ease-in-out;

                &:hover {
                    color: $--color-primary;
                }
            }
        }

        &-action {
            margin-left: auto;
            align-self: stretch;
            gap: 10px;
            display: flex;

            svg {
                cursor: pointer;
                transition: color .25s ease-in-out;

                &:hover {
                    color: $--color-primary;
                }
            }
        }
    }
</style>