# Forum Project

**Framework:** CodeIgniter v4.6.1

## 🚀 Contribuintes

* Arthur Romanato Moro
* Lucas Abati Zanotto

---

## 📋 Descrição

Este é um sistema de fórum construído sobre o **CodeIgniter** (v4.6.1), que inclui funcionalidades de:

* Gerenciamento de usuários (registro, autenticação, perfis, banimentos)
* Comunidades (criação, banimento, solicitação de ingresso)
* Posts e comentários (CRUD, votos, visualizações, anexos)
* Mensagens diretas
* Relacionamentos user<->community

---

## 🔧 Pré-requisitos

* PHP >= 8.0
* Composer
* MySQL
* Extensões PHP: `intl`, `mbstring`, `pdo_mysql`, `curl`
* Servidor Web (Apache, Nginx ou embutido)

---

## 🏗️ Instalação e Configuração

1. **Clone o repositório:**

   ```bash
   git clone https://github.com/ArtMor2020/prog3-ProjetoPrincipal.git
   cd ProjectForum
   ```

2. **Instale as dependências Composer:**

   ```bash
   composer install
   ```

3. **Copie o arquivo de ambiente e edite:**

   ```bash
   cp env .env
   ```

   Configure as variáveis `database.default.*` no `.env` (host, username, password, database).

4. **Crie o banco de dados e rode as migrations:**

   ```bash
   php spark migrate
   ```

5. **Popule com dados de teste (seeders):**

   ```bash
   php spark db:seed DatabaseSeeder
   ```

---

## ▶️ Executando o Servidor

Você pode usar o servidor embutido do PHP:

```bash
php spark serve
```

Acesse em [http://localhost:8080](http://localhost:8080).

---

## 📐 Design do Banco de Dados

Veja o diagrama das tabelas neste link:

> [https://excalidraw.com/#room=47268d9fafe3e3264aed,jBRXUdYh24DLouXEuZmBLA](https://excalidraw.com/#room=47268d9fafe3e3264aed,jBRXUdYh24DLouXEuZmBLA)

---

## 🤝 Contribuindo

1. Fork no repositório
2. Crie uma branch para sua feature (`git checkout -b feature/nova-funcionalidade`)
3. Commit suas alterações (`git commit -m 'feat: descrição da feature'`)
4. Push para sua branch (`git push origin feature/nova-funcionalidade`)
5. Abra um Pull Request

