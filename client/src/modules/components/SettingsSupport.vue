<!-- eslint-disable vue/no-deprecated-v-on-native-modifier -->
<template>
    <div class="container">
        <div class="container__item">
            <div class="container__title">Skorzystaj z pomocy technicznej</div>
            <div class="chat-container" ref="chat">
                <div v-for="message in chatMessages" :key="message.id" class="chat-message">
                    <div class="chat-message__author">{{ message.author }}</div>
                    <div class="chat-message__content">{{ message.content }}</div>
                </div>
            </div>

            <el-input
                v-model="newMessage"
                :rows="3"
                resize="none"
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
                    <div class="contact-icon">
                        <font-awesome-icon icon="phone"/>
                    </div>
                    <p class="contact-info">Infolinia: <span>123-456-789</span></p>
                </div>
                <div class="contact-row">
                    <div class="contact-icon">
                        <font-awesome-icon icon="envelope"/>
                    </div>
                    <p class="contact-info">Email: <span>ad-menager@example.com</span></p>
                </div>
                <div class="contact-row">
                    <div class="contact-icon">
                        <font-awesome-icon icon="map-marker-alt"/>
                    </div>
                    <p class="contact-info">Adres: <span>ul. Przykładowa 123, 00-001 Warszawa</span></p>
                </div>
                <div class="contact-row">
                    <div class="contact-icon">
                        <font-awesome-icon icon="id-card"/>
                    </div>
                    <p class="contact-info">NIP: <span>816-163-08-90</span></p>
                </div>
                <div class="contact-row">
                    <div class="contact-icon">
                        <font-awesome-icon icon="clock"/>
                    </div>
                    <p class="contact-info">Godziny otwarcia infolinii: <span>Poniedziałek-Piątek: 9:00-17:00</span></p>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
    import { ref } from 'vue';
    import { useAuthStore } from '@/stores/AuthStore';

    const authStore = useAuthStore();

    const chat = ref('');

    const chatMessages = ref([
        { id: 1, author: 'System', content: 'Witaj! W czym mogę pomóc?' },
        { id: 2, author: '', content: '' }
    ]);

    const newMessage = ref('');

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
            scrollChatToBottom()
        }
    };

    const handleEnterKey = (event) => {
        if (event.keyCode === 13 && !event.shiftKey) {
            sendMessage()
        }
    };

    const getRandomResponse = () => {
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
    };

    const scrollChatToBottom = () => {
        setTimeout(() => { chat.value.scrollTop = chat.value.scrollHeight }, 1);
    };
</script>

<style scoped lang="scss">
    .container__contact {
        margin-top: 20px;

        .contact-row {
            display: flex;
            align-items: center;
            margin-bottom: 10px;

            .contact-icon {
                width: 20px;
                display: flex;
                justify-content: center;
                align-items: center;
                margin: 0px 10px 5px 0px;
            }

            .contact-info {
                font-size: 1.6rem;

                span {
                    font-size: 1.4rem;
                    color: $--color-text-muted-1;
                }
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
            font-size: 1.6rem;
            color: $--color-text;
            font-weight: bold;
            margin-bottom: 20px;
        }

        &__description {
            color: $--color-text;
            font-size: 1.4rem;
        }

        &__button {
            margin-top: 20px;
        }
    }

    .form {
        width: 100%;

        &__title {
            font-size: 2.4rem;
            font-weight: bold;
            color: $--color-text;
            margin-bottom: 20px;
        }

        &__title--big {
            font-size: 3.2rem;
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
                font-size: 1.4rem;
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
