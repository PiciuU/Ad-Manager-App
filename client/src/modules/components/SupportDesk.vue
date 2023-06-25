<!-- eslint-disable vue/no-deprecated-v-on-native-modifier -->
<template>
    <div class="container">
        <div class="container__item">
            <div class="container__title">Skorzystaj z pomocy technicznej</div>
            <div class="chat-container" ref="chatContainer">
                <div v-for="message in chatMessages" :key="message.id" class="chat-message">
                    <div class="chat-message__author">{{ message.author }}</div>
                    <div class="chat-message__content">{{ message.content }}</div>
                </div>
            </div>

            <el-input
                v-model="newMessage"
                :autosize="{ minRows: 2, maxRows: 4 }"
                type="textarea"
                placeholder="Napisz wiadomość..."
                @keyup.enter.native="handleEnterKey($event)"
            />
            <el-button class="card__buttons--left" @click="sendMessage" type="primary">
                Wyślij
            </el-button>
        </div>

        <div class="container__item">
            <div class="container__title">Skontaktuj się z nami</div>
            <div class="container__contact">
                <div class="contact-row">
                    <font-awesome-icon
                        class="fa fa-phone"
                        icon="fa fa-phone"
                        style="margin: 5px"
                    ></font-awesome-icon>
                    <span class="contact-info">Infolinia: {{ contactInfo.phone }}</span>
                </div>
                <div class="contact-row">
                    <font-awesome-icon
                        class="fa-envelope"
                        icon="fa-envelope"
                        style="margin: 5px"
                    ></font-awesome-icon>
                    <span class="contact-info">Email: {{ contactInfo.email }}</span>
                </div>
                <div class="contact-row">
                    <font-awesome-icon
                        class="fa-map-marker-alt"
                        icon="fa-map-marker-alt"
                        style="margin: 5px"
                    ></font-awesome-icon>
                    <span class="contact-info">Adres: {{ contactInfo.address }}</span>
                </div>
                <div class="contact-row">
                    <font-awesome-icon
                        class="fa-id-card"
                        icon="fa-id-card"
                        style="margin: 5px"
                    ></font-awesome-icon>
                    <span class="contact-info">NIP: {{ contactInfo.nip }}</span>
                </div>
                <div class="contact-row">
                    <font-awesome-icon
                        class="fa-clock"
                        icon="fa-clock"
                        style="margin: 5px"
                    ></font-awesome-icon>
                    <span class="contact-info"
                        >Godziny otwarcia infolinii: {{ contactInfo.openingHours }}</span
                    >
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import { ref, onMounted } from 'vue'
import { useAuthStore } from '@/stores/AuthStore'
import { library } from '@fortawesome/fontawesome-svg-core'
import {
    faPhone,
    faEnvelope,
    faMapMarkerAlt,
    faIdCard,
    faClock
} from '@fortawesome/free-solid-svg-icons'

library.add(faPhone, faEnvelope, faMapMarkerAlt, faIdCard, faClock)

export default {
    setup() {
        const chatMessages = ref([
            { id: 1, author: 'System', content: 'Witaj! W czym mogę pomóc?' },
            { id: 2, author: '', content: '' }
        ])

        const newMessage = ref('')

        const authStore = useAuthStore()

        const sendMessage = () => {
            if (newMessage.value.trim() !== '') {
                const userMessage = {
                    id: chatMessages.value.length + 1,
                    author: `${authStore.getUser.login} (${authStore.getUser.name})`,
                    content: newMessage.value.trim()
                }
                chatMessages.value.push(userMessage)

                // Losowanie odpowiedzi
                const responseMessage = {
                    id: chatMessages.value.length + 1,
                    author: 'System',
                    content: getRandomResponse()
                }
                chatMessages.value.push(responseMessage)

                newMessage.value = ''

                // Scroll do ostatniej wiadomości
                scrollToBottom()
            }
        }

        const handleEnterKey = (event) => {
            if (event.keyCode === 13 && !event.shiftKey) {
                sendMessage()
            }
        }

        const contactInfo = ref({
            phone: '123-456-789',
            email: 'ad-menager@example.com',
            address: 'ul. Przykładowa 123, 00-001 Warszawa',
            nip: '816-163-08-90',
            openingHours: 'Poniedziałek-Piątek: 9:00-17:00'
        })

        function getRandomResponse() {
            const responses = [
                'Dziękujemy za wiadomość! Odpowiemy tak szybko, jak to możliwe.',
                'Przepraszamy za wszelkie niedogodności. Proszę czekać na naszą odpowiedź.',
                'Rozumiem twoje zapytanie. Sprawdzam i odpowiadam wkrótce.',
                'Czy mogę prosić o więcej szczegółów? Chętnie Ci pomogę.',
                'Proszę o większe doprecyzowanie pytania bo nie rozumiem',
                'Otrzymaliśmy twoją wiadomość. Skontaktujemy się z tobą wkrótce.',
                'Dziękujemy za cierpliwość. Pracujemy nad udzieleniem odpowiedzi.',
                'Witamy! Jak możemy ci pomóc? Odpowiedź jest w drodze.',
                'Przykro nam, ale nie posiadamy informacji na ten temat. Czy mogę pomóc w czymś innym?',
                'Dziękujemy za zainteresowanie naszym produktem/usługą. Udzielimy odpowiedzi wkrótce.',
                'Twoje pytanie zostało przekazane do odpowiedniego zespołu. Odpowiemy niebawem.'
            ]
            const index = Math.floor(Math.random() * responses.length)
            return responses[index]
        }

        function scrollToBottom() {
            const chatContainer = document.querySelector('.chat-container')
            if (chatContainer) {
                chatContainer.scrollTop = chatContainer.scrollHeight
            }
        }

        onMounted(() => {
            scrollToBottom()
        })

        return {
            chatMessages,
            newMessage,
            sendMessage,
            handleEnterKey,
            contactInfo
        }
    }
}
</script>

<style scoped lang="scss">
.container__contact {
    margin-top: 20px;

    .contact-row {
        display: flex;
        align-items: center;
        margin-bottom: 10px;

        i {
            margin-right: 10px;
        }

        .contact-info {
            font-size: 14px;
        }
    }
}

.container {
    display: flex;
    gap: 50px;

    &__item {
        position: relative;
        display: flex;
        flex-direction: column;
        align-items: flex-start;
        width: 50%;
        margin: 10px 0px;
        padding: 0px 10px;
        text-align: left;

        &:first-child:after {
            content: '';
            position: absolute;
            width: 1px;
            height: 100%;
            background: $--color-text-muted-3;
            right: -25px;
        }
    }

    &__title {
        position: relative;
        font-size: 16px;
        color: $--color-text;
        font-weight: bold;
        margin-bottom: 20px;
    }

    &__description {
        color: $--color-text;
        font-size: 14px;
    }

    &__button {
        margin-top: 20px;
    }
}

.form {
    width: 100%;

    &__title {
        font-size: 24px;
        font-weight: bold;
        color: $--color-text;
        margin-bottom: 20px;
    }

    &__title--big {
        font-size: 32px;
        text-align: center;
    }

    &__links {
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 15px;
    }
}

@media screen and (max-width: 1000px) {
    .container {
        flex-direction: column;
        gap: 40px;

        &__item {
            width: 100%;

            &:first-child:after {
                width: 100%;
                height: 1px;
                background: $--color-text;
                right: unset;
                bottom: -35px;
            }
        }
    }

    .form {
        width: 90%;
    }
}

@media screen and (max-width: $--breakpoint-mobile) {
    .container__item {
        padding: 0px;
    }

    .container__title {
        margin-bottom: 10px;
    }

    .form {
        width: 100%;
    }

    .el-form-item {
        flex-direction: column;
        margin-bottom: 15px;
    }
}

.chat-container {
    height: 200px;
    overflow-y: scroll;
    border: 1px solid $--color-text-muted-3;
    padding: 10px;
    margin-bottom: 10px;
    border-radius: 2px;
    width: 100%;
    &::-webkit-scrollbar {
        width: 8px;
        background-color: $--color-overlay;
    }

    &::-webkit-scrollbar-thumb {
        background-color: $--color-text-muted-2;
        border-radius: 4px;
    }

    .chat-message {
        margin-bottom: 10px;

        &__author {
            font-weight: bold;
        }

        &__content {
            margin-top: 5px;
        }
    }

    .chat-message__author {
        color: $--color-primary;
    }

    .chat-message__content {
        color: $--color-text;
    }
}

.card__buttons--left {
    margin-top: 10px;
}
</style>
