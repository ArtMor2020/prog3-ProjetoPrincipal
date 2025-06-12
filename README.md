# Forum Application

**Backend:**  [CodeIgniter v4.6.1](/Backend)
**Frontend:** React + Vite

---

## 🚀 Contribuintes

* Arthur Romanato Moro
* Lucas Abati Zanotto

---

## 📋 Visão Geral

Este projeto é um fórum completo dividido em *backend* e *frontend*, com recursos como:

* **Usuários:** registro, login, perfil, privacidade, banimento e soft‑delete
* **Comunidades:** criação, privacidade, banimento, solicitações e convites
* **Posts & Comentários:** CRUD, votos, contagem de visualizações e anexos
* **Mensagens Diretas:** envio, marcação como lido
* **Serviços RESTful:** endpoints dedicados para cada entidade

---

## 🔧 Pré‑requisitos Backend

* PHP >= 8.0
* Composer
* MySQL ou MariaDB
* Extensões PHP: `intl`, `mbstring`, `pdo_mysql`, `curl`

### Instalação Backend

1. Clone e entre na pasta:

   ```bash
   git clone https://github.com/ArtMor2020/prog3-ProjetoPrincipal.git
   cd Backend
   ```
2. Instale dependências:

   ```bash
   composer install
   ```
3. Copie e ajuste o `.env`:

   ```bash
   cp env .env
   ```

   Defina `database.default.*` com suas credenciais.
4. Crie o banco e execute migrações:

   ```bash
   php spark migrate
   ```
5. (Opcional) Seeders:

   ```bash
   php spark db:seed DatabaseSeeder
   ```

### Executando API

```bash
php spark serve
```

A API ficará disponível em `http://localhost:8080`.

---

## 🔧 Pré‑requisitos Frontend

* Node.js >= 16
* npm ou yarn

### Instalação Frontend

1. Entre na pasta:

   ```bash
   cd Frontend
   ```
2. Instale dependências:

   ```bash
   npm install
   # ou yarn
   ```
3. Rode o servidor de desenvolvimento:

   ```bash
   npm run dev
   ```

A aplicação roda em `http://localhost:5173` por padrão.

---

## 🔄 Fluxo de Desenvolvimento

### Backend

* Endpoints RESTful organizados por controlador/repositório/serviço.
* CORS configurado em `app/Config/Cors.php` para permitir chamadas do frontend.

### Frontend

* React Router para rotas: `/login`, `/register`, `/home`, `/posts/:id`, `/users/:id`, `/communities/:id`, `/post/create`, `/community/create`
* `UserContext` armazena informações do usuário logado.
* Componentes reutilizáveis: `Header`, `PostCard`, `CommentCard`, `PostVoteHeader`.

---

## 📐 Design do Banco de Dados

> [Excalidraw com os diagramas das tabelas](https://excalidraw.com/#room=47268d9fafe3e3264aed,jBRXUdYh24DLouXEuZmBLA)

---

## 🎨 Wireframes Frontend

> [Excalidraw com telas e navegação](https://excalidraw.com/#room=a8db75fd296f86d44e95,Rfbj5Ruwn0MRNsRFFbL4DA)

---

## 🤝 Como Contribuir

1. Fork no repositório
2. Crie uma branch: `git checkout -b feature/nova-funcionalidade`
3. Commit suas alterações: `git commit -m 'feat: descrição'`
4. Push: `git push origin feature/nova-funcionalidade`
5. Abra um Pull Request

---

## 📝 Licença

Este projeto é licenciado sob a MIT License.
