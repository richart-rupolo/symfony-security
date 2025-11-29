# Symfony Security – MongoDB Queryable Encryption (Test Project)

## 🔐 Overview

This repository is a **technical experiment** demonstrating how Symfony integrates with **MongoDB Queryable Encryption** using a **local primary node** instead of MongoDB Atlas.
The objective is to explore how encrypted fields behave in real queries, how the PHP driver handles client‑side encryption, and what is or isn’t supported when running outside Atlas.

This project is intentionally simple: **a study of secure patterns**, not a production banking system.
Based on the original concept demonstrated here:
[https://www.youtube.com/watch?v=UuknxVdqzb4](https://www.youtube.com/watch?v=UuknxVdqzb4)

---

## 🎯 Goals of This Project

* Understand Symfony + MongoDB Queryable Encryption end‑to‑end.
* Test encrypted inserts, reads, and equality queries.
* Demonstrate how the key vault and KMS configuration behave locally.
* Show the limitations of range queries without Atlas.
* Provide a clean reference for encrypted documents using Doctrine ODM.

---

## ⚠️ Limitations (Important)

### ❌ Range Queries Do Not Work Locally

This project includes a **transaction search by amount range**, but:

* **Range queries on encrypted fields require MongoDB Atlas**.
* Local MongoDB currently supports **only equality queries** with Queryable Encryption.

So in this repo:

* Equality queries → **work** ✔️
* Range queries → **do not work** ❌ (Atlas-only feature)

The goal was to prove this behavior in practice.

---

## 🧱 Tech Stack

* PHP 8+
* Symfony 7
* Doctrine MongoDB ODM
* MongoDB PHP Driver
* Local MongoDB (single primary)
* Docker (optional)

---

## ⚙️ Installation

```bash
git clone https://github.com/richart-rupolo/symfony-security.git
cd symfony-security
composer install
```

### Start Local MongoDB

```bash
docker run -d \
  --name mongo \
  -p 27017:27017 \
  mongo:latest
```

### Run Symfony

```bash
symfony server:start
```

Or manually:

```bash
php -S localhost:8080 -t public
```

---

# 🧩 Architecture Diagram (ASCII)

```text
┌───────────────────────────┐        ┌───────────────────────────┐
│        Symfony App        │        │     Local MongoDB Node    │
│  (Controllers + Services) │        │         (Primary)          │
└──────────────┬────────────┘        └──────────────┬────────────┘
               │ Client-Side Encryption              │
               │ (automatic via driver)              │
               ▼                                      ▼
       ┌────────────────┐                   ┌──────────────────────┐
       │ Doctrine ODM   │  Encrypted BSON   │  Encrypted Collection │
       │ (Mapping)      │ ────────────────▶ │  (ciphertext fields) │
       └────────────────┘                   └──────────────────────┘
               ▲                                      ▲
               │ Decrypted on read                    │
               │ (driver-managed)                     │
               │                                      │
       ┌───────────────────┐              ┌─────────────────────────┐
       │ Key Vault         │◀────────────▶│ Local KMS / Key Provider│
       │ (keys for fields) │              │   (local file or dir)   │
       └───────────────────┘              └─────────────────────────┘
```

---

# 🔐 Encryption Flow (Visual)

```text
[1] Developer inserts a Transaction
        │
        ▼
[2] Symfony → Doctrine ODM
        │
        ▼
[3] MongoDB Client encrypts fields
    - amount
    - description
    - accountId
        │
        ▼
[4] Encrypted BSON is written to MongoDB
        │
        ▼
[5] When reading:
    MongoDB Client decrypts automatically
        │
        ▼
[6] Symfony receives plaintext objects
```

### Equality Query Flow

```text
WHERE amount == X
  ↓
Driver encrypts X → ciphertext
  ↓
MongoDB finds matching ciphertext
  ↓
Driver decrypts results
```

### Range Query Flow (Why it Fails Locally)

```text
WHERE amount BETWEEN A AND B
  ↓
Driver CANNOT generate searchable ciphertext for ranges
  ↓
Requires Atlas Queryable Encryption (Range Indexes)
  ↓
Local MongoDB returns no results
```

---

# 📂 Project Structure

```text
src/
  Controller/
  Document/
  Service/
config/
templates/
```

Documents contain encrypted fields using attributes from Doctrine ODM.
Services handle key vault initialization and driver configuration.

---

# 🔍 Implemented Features

### ✔️ Works

* Account creation with encrypted fields
* Transaction creation
* Encrypted storage using Client-Side Field Level Encryption
* Equality search on encrypted fields
* Local primary node support

### ❌ Does Not Work (Expected)

* Range queries on encrypted data
* Atlas-only encrypted index operators

---

# 🧪 Purpose & Notes

This repository exists strictly as a **learning lab** to understand the internals of:

* Symfony + MongoDB encryption
* ODM mapping of encrypted fields
* Encryption key management
* Limitations of local MongoDB vs Atlas
* Real‑world behavior of secure financial-like data

Not meant for production.
No license restrictions.
Pure freedom.

---

# 📜 License

**None. Totally unlicensed. Public domain. Free for anyone to copy, break, remix, fork, or repurpose.**

---

# 👤 Author

Created by **Richart Rupolo** as a practical test environment to explore secure design and encrypted queries using Symfony and MongoDB.
