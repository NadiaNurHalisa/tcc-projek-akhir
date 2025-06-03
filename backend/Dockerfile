# Gunakan image Node.js resmi
FROM node:18-alpine

# Buat direktori kerja
WORKDIR /usr/src/app

# Salin package.json dan package-lock.json
COPY package*.json ./

# Install dependencies
RUN npm install

# Salin semua file proyek
COPY . .

# Expose port yang digunakan Express.js (misalnya 8080)
EXPOSE 8080

# Jalankan aplikasi
CMD ["npm", "start"]